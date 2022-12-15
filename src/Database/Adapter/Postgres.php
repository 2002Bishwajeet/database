<?php

namespace Utopia\Database\Adapter;

use PDO;
use Exception;
use PDOException;
use Throwable;
use Utopia\Database\Database;
use Utopia\Database\Document;
use Utopia\Database\Exception\Duplicate;
use Utopia\Database\Query;
use Utopia\Database\Validator\Authorization;
use Utopia\Database\ID;

class Postgres extends MariaDB
{
    /**
     * Differences between MariaDB and Postgres
     * 
     * 1. Need to use CASCADE to DROP schema
     * 2. Quotes are different ` vs "
     * 3. DATETIME is TIMESTAMP
     */

    /**
     * Create Database
     *
     * @param string $name
     * 
     * @return bool
     */
    public function create(string $name): bool
    {
        $name = $this->filter($name);

        return $this->getPDO()
            ->prepare("CREATE SCHEMA IF NOT EXISTS \"{$name}\"")
            ->execute();
    }

   /**
     * Delete Database
     *
     * @param string $name
     * @return bool
     * @throws Exception
     * @throws PDOException
     */
    public function delete(string $name): bool
    {
        $name = $this->filter($name);
        return $this->getPDO()
            ->prepare("DROP {$this->getSQLSchemaKeyword()} {$this->getSQLQuote()}{$name}{$this->getSQLQuote()} CASCADE;")
            ->execute();
    }

    /**
     * Create Collection
     * 
     * @param string $name
     * @param Document[] $attributes (optional)
     * @param Document[] $indexes (optional)
     * @return bool
     */
    public function createCollection(string $name, array $attributes = [], array $indexes = []): bool
    {
        $database = $this->getDefaultDatabase();
        $namespace = $this->getNamespace();
        $id = $this->filter($name);

        $this->getPDO()->beginTransaction();

        foreach ($attributes as &$attribute) {
            $attrId = $this->filter($attribute->getId());
            $attrType = $this->getSQLType($attribute->getAttribute('type'), $attribute->getAttribute('size', 0), $attribute->getAttribute('signed', true));

            if($attribute->getAttribute('array')) {
                $attrType = 'TEXT';
            }

            $attribute = "\"{$attrId}\" {$attrType}, ";
        }

        $stmt = $this->getPDO()
            ->prepare("CREATE TABLE IF NOT EXISTS \"{$database}\".\"{$namespace}_{$id}\" (
                \"_id\" SERIAL NOT NULL,
                \"_uid\" VARCHAR(255) NOT NULL,
                \"_createdAt\" TIMESTAMP DEFAULT NULL,
                \"_updatedAt\" TIMESTAMP DEFAULT NULL,
                \"_permissions\" TEXT DEFAULT NULL,
                " . \implode(' ', $attributes) . "
                PRIMARY KEY (\"_id\")
                )");
//,
//INDEX (\"_createdAt\"),
//INDEX (\"_updatedAt\")
        $stmtIndex = $this->getPDO()
            ->prepare("CREATE UNIQUE INDEX \"index_{$namespace}_{$id}_uid\" on \"{$database}\".\"{$namespace}_{$id}\" (LOWER(_uid));");
        try{
            $stmt->execute();
            $stmtIndex->execute();

            $this->getPDO()
                ->prepare("CREATE TABLE IF NOT EXISTS \"{$database}\".\"{$namespace}_{$id}_perms\" (
                        \"_id\" SERIAL NOT NULL,
                        \"_type\" VARCHAR(12) NOT NULL,
                        \"_permission\" VARCHAR(255) NOT NULL,
                        \"_document\" VARCHAR(255) NOT NULL,
                        PRIMARY KEY (\"_id\")
                    )")
                ->execute();

            foreach ($indexes as &$index) {
                $indexId = $this->filter($index->getId()); 
                $indexAttributes = $index->getAttribute('attributes');
    
                $this->createIndex($id, $indexId, $index->getAttribute('type'), $indexAttributes, [], $index->getAttribute("orders"));
            }
        }catch(Exception $e){
            $this->getPDO()->rollBack();
            throw new Exception('Failed to create collection: '.$e->getMessage());
        }
        
        if(!$this->getPDO()->commit()) {
            throw new Exception('Failed to commit transaction');
        }
        
        // Update $this->getIndexCount when adding another default index
        // return $this->createIndex($id, "_index2_{$namespace}_{$id}", Database::INDEX_FULLTEXT, ['_read'], [], []);

        return true;
    }

    /**
     * Delete Collection
     * 
     * @param string $id
     * @return bool
     */
    public function deleteCollection(string $id): bool
    {
        $id = $this->filter($id);

        return $this->getPDO()
            ->prepare("DROP TABLE {$this->getSQLTable($id)}, {$this->getSQLTable($id . '_perms')};")
            ->execute();
    }

    /**
     * Create Attribute
     * 
     * @param string $collection
     * @param string $id
     * @param string $type
     * @param int $size
     * @param bool $array
     * 
     * @return bool
     */
    public function createAttribute(string $collection, string $id, string $type, int $size, bool $signed = true, bool $array = false): bool
    {
        $name = $this->filter($collection);
        $id = $this->filter($id);
        $type = $this->getSQLType($type, $size, $signed);

        if($array) {
            $type = 'TEXT';
        }

        return $this->getPDO()
            ->prepare("ALTER TABLE {$this->getSQLTable($name)}
                ADD COLUMN \"{$id}\" {$type};")
            ->execute();
    }

    /**
     * Delete Attribute
     * 
     * @param string $collection
     * @param string $id
     * @param bool $array
     * 
     * @return bool
     */
    public function deleteAttribute(string $collection, string $id, bool $array = false): bool
    {
        $name = $this->filter($collection);
        $id = $this->filter($id);

        return $this->getPDO()
            ->prepare("ALTER TABLE {$this->getSQLTable($name)}
                DROP COLUMN \"{$id}\";")
            ->execute();
    }

    /**
     * Rename Attribute
     *
     * @param string $collection
     * @param string $old
     * @param string $new
     * @return bool
     * @throws Exception
     * @throws PDOException
     */
    public function renameAttribute(string $collection, string $old, string $new): bool
    {
        $collection = $this->filter($collection);
        $old = $this->filter($old);
        $new = $this->filter($new);

        return $this->getPDO()
            ->prepare("ALTER TABLE {$this->getSQLTable($collection)} RENAME COLUMN
                {$this->getSQLQuote()}{$old}{$this->getSQLQuote()}
                TO
                {$this->getSQLQuote()}{$new}{$this->getSQLQuote()};")
            ->execute();
    }

    /**
     * Create Index
     * 
     * @param string $collection
     * @param string $id
     * @param string $type
     * @param array $attributes
     * @param array $lengths
     * @param array $orders
     * 
     * @return bool
     */
    public function createIndex(string $collection, string $id, string $type, array $attributes, array $lengths, array $orders): bool
    {
        $name = $this->filter($collection);
        $id = $this->filter($id);

        $attributes = \array_map(fn ($attribute) => match ($attribute) {
            '$id' =>'_uid',
            '$createdAt' => '_createdAt',
            '$updatedAt' => '_updatedAt',
            default => $attribute
        }, $attributes);

        foreach($attributes as $key => &$attribute) {
            $length = $lengths[$key] ?? '';
            $length = (empty($length)) ? '' : '('.(int)$length.')';
            $order = $orders[$key] ?? '';
            $attribute = $this->filter($attribute);

            if(Database::INDEX_FULLTEXT === $type) {
                $order = '';
            }

            $attribute = "\"{$attribute}\" {$order}";
        }

        return $this->getPDO()
            ->prepare($this->getSQLIndex($name, $id, $type, $attributes))
            ->execute();
    }

    /**
     * Delete Index
     * 
     * @param string $collection
     * @param string $id
     * 
     * @return bool
     */
    public function deleteIndex(string $collection, string $id): bool
    {
        $name = $this->filter($collection);
        $id = $this->filter($id);
        $schemaName = $this->getDefaultDatabase();

        return $this->getPDO()
            ->prepare("DROP INDEX IF EXISTS \"{$schemaName}\".{$id};")
            ->execute();
    }

    /**
     * Rename Index
     *
     * @param string $collection
     * @param string $old
     * @param string $new
     * @return bool
     * @throws Exception
     * @throws PDOException
     */
    public function renameIndex(string $collection, string $old, string $new): bool
    {
        $collection = $this->filter($collection);
        $old = $this->filter($old);
        $new = $this->filter($new);
        $schemaName = $this->getDefaultDatabase();

        //
        return $this->getPDO()
            ->prepare("ALTER INDEX \"{$schemaName}\".{$old} RENAME TO {$this->getSQLQuote()}{$new}{$this->getSQLQuote()};")
            ->execute();
    }

     /**
     * Create Document
     *
     * @param string $collection
     * @param Document $document
     *
     * @return Document
     */
    public function createDocument(string $collection, Document $document): Document
    {
        $attributes = $document->getAttributes();
        $attributes['_createdAt'] = $document->getCreatedAt();
        $attributes['_updatedAt'] = $document->getUpdatedAt();
        $attributes['_permissions'] = json_encode($document->getPermissions());

        $name = $this->filter($collection);
        $columns = '';
        $columnNames = '';

        $this->getPDO()->beginTransaction();

        /**
         * Insert Attributes
         */
        $bindIndex = 0;
        foreach ($attributes as $attribute => $value) { // Parse statement
            $column = $this->filter($attribute);
            $bindKey = 'key_' . $bindIndex;
            $columns .= "{$this->getSQLQuote()}{$column}{$this->getSQLQuote()}, ";
            $columnNames .= ':' . $bindKey . ', ';
            $bindIndex++;
        }

        $stmt = $this->getPDO()
            ->prepare("INSERT INTO {$this->getSQLTable($name)}
                ({$columns}\"_uid\") VALUES ({$columnNames}:_uid)");

        $stmt->bindValue(':_uid', $document->getId(), PDO::PARAM_STR);

        $attributeIndex = 0;
        foreach ($attributes as $attribute => $value) {
            if (is_array($value)) { // arrays & objects should be saved as strings
                $value = json_encode($value);
            }

            $bindKey = 'key_' . $attributeIndex;
            $attribute = $this->filter($attribute);
            $value = (is_bool($value)) ? ($value == true ? "true" : "false") : $value;
            $stmt->bindValue(':' . $bindKey, $value, $this->getPDOType($value));
            $attributeIndex++;
        }

        $permissions = [];
        foreach (Database::PERMISSIONS as $type) {
            foreach ($document->getPermissionsByType($type) as $permission) {
                $permission = \str_replace('"', '', $permission);
                $permissions[] = "('{$type}', '{$permission}', '{$document->getId()}')";
            }
        }

        
        if (!empty($permissions)) {
            $queryPermissions = "INSERT INTO {$this->getSQLTable($name.'_perms')}
            (_type, _permission, _document) VALUES " . implode(', ', $permissions);
            $stmtPermissions = $this->getPDO()->prepare($queryPermissions);
        }

        try {
            $stmt->execute();

            $document['$internalId'] = $this->getDocument($collection, $document->getId())->getInternalId();

            if (isset($stmtPermissions)) {
                $stmtPermissions->execute();
            }
        } catch (Throwable $e) {
            switch ($e->getCode()) {
                case 1062:
                case 23505:
                    $this->getPDO()->rollBack();
                    throw new Duplicate('Duplicated document: '.$e->getMessage());
                    break;

                default:
                    throw $e;
                    break;
            }
        }

        if(!$this->getPDO()->commit()) {
            throw new Exception('Failed to commit transaction');
        }

        return $document;
    }

    /**
     * Update Document
     *
     * @param string $collection
     * @param Document $document
     *
     * @return Document
     */
    public function updateDocument(string $collection, Document $document): Document
    {
        $attributes = $document->getAttributes();
        $attributes['_createdAt'] = $document->getCreatedAt();
        $attributes['_updatedAt'] = $document->getUpdatedAt();
        $attributes['_permissions'] = json_encode($document->getPermissions());

        $name = $this->filter($collection);
        $columns = '';

        /**
         * Get current permissions from the database
         */
        $permissionsStmt = $this->getPDO()->prepare("
                SELECT _type, _permission
                FROM {$this->getSQLTable($name.'_perms')} p
                WHERE p._document = :_uid
        ");
        $permissionsStmt->bindValue(':_uid', $document->getId());
        $permissionsStmt->execute();
        $permissions = $permissionsStmt->fetchAll();

        $initial = [];
        foreach (Database::PERMISSIONS as $type) {
            $initial[$type] = [];
        }

        $permissions = array_reduce($permissions, function (array $carry, array $item) {
            $carry[$item['_type']][] = $item['_permission'];

            return $carry;
        }, $initial);

        $this->getPDO()->beginTransaction();

        /**
         * Get removed Permissions
         */
        $removals = [];
        foreach(Database::PERMISSIONS as $type) {
            $diff = \array_diff($permissions[$type], $document->getPermissionsByType($type));
            if (!empty($diff)) {
                $removals[$type] = $diff;
            }
        }

        /**
         * Get added Permissions
         */
        $additions = [];
        foreach(Database::PERMISSIONS as $type) {
            $diff = \array_diff($document->getPermissionsByType($type), $permissions[$type]);
            if (!empty($diff)) {
                $additions[$type] = $diff;
            }
        }

        /**
         * Query to remove permissions
         */
        $removeQuery = '';
        if (!empty($removals)) {
            $removeQuery = 'AND (';
            foreach ($removals as $type => $permissions) {
                $removeQuery .= "(
                    _type = '{$type}'
                    AND _permission IN (" . implode(', ', \array_map(fn(string $i) => ":_remove_{$type}_{$i}", \array_keys($permissions))) . ")
                )";
                if ($type !== \array_key_last($removals)) {
                    $removeQuery .= ' OR ';
                }
            }
        }
        if (!empty($removeQuery)) {
            $removeQuery .= ')';
            $stmtRemovePermissions = $this->getPDO()
                ->prepare("
                DELETE
                FROM {$this->getSQLTable($name.'_perms')}
                WHERE
                    _document = :_uid
                    {$removeQuery}
            ");
            $stmtRemovePermissions->bindValue(':_uid', $document->getId());

            foreach ($removals as $type => $permissions) {
                foreach ($permissions as $i => $permission) {
                    $stmtRemovePermissions->bindValue(":_remove_{$type}_{$i}", $permission);
                }
            }
        }

        /**
         * Query to add permissions
         */
        if (!empty($additions)) {
            $values = [];
            foreach ($additions as $type => $permissions) {
                foreach ($permissions as $i => $_) {
                    $values[] = "( :_uid, '{$type}', :_add_{$type}_{$i} )";
                }
            }

            $stmtAddPermissions = $this->getPDO()
                ->prepare(
                    "INSERT INTO {$this->getSQLTable($name.'_perms')}
                    (_document, _type, _permission) VALUES" . \implode(', ', $values)
                );

            $stmtAddPermissions->bindValue(":_uid", $document->getId());
            foreach ($additions as $type => $permissions) {
                foreach ($permissions as $i => $permission) {
                    $stmtAddPermissions->bindValue(":_add_{$type}_{$i}", $permission);
                }
            }
        }

        /**
         * Update Attributes
         */

        $bindIndex = 0;
        foreach ($attributes as $attribute => $value) {
            $column = $this->filter($attribute);
            $bindKey = 'key_' . $bindIndex;
            $columns .= "\"{$column}\"" . '=:' . $bindKey . ',';
            $bindIndex++;
        }

        $stmt = $this->getPDO()
            ->prepare("UPDATE {$this->getSQLTable($name)}
                SET {$columns} _uid = :_uid WHERE _uid = :_uid");

        $stmt->bindValue(':_uid', $document->getId());

        $attributeIndex = 0;
        foreach ($attributes as $attribute => $value) {
            if (is_array($value)) { // arrays & objects should be saved as strings
                $value = json_encode($value);
            }

            $bindKey = 'key_' . $attributeIndex;
            $attribute = $this->filter($attribute);
            $value = (is_bool($value)) ? ($value == true ? "true" : "false") : $value;
            $stmt->bindValue(':' . $bindKey, $value, $this->getPDOType($value));
            $attributeIndex++;
        }

        if (!empty($attributes)) {
            try {
                $stmt->execute();
                if (isset($stmtRemovePermissions)) {
                    $stmtRemovePermissions->execute();
                }
                if (isset($stmtAddPermissions)) {
                    $stmtAddPermissions->execute();
                }
            } catch (PDOException $e) {
                $this->getPDO()->rollBack();
                switch ($e->getCode()) {
                    case 1062:
                    case 23000:
                        throw new Duplicate('Duplicated document: ' . $e->getMessage());

                    default:
                        throw $e;
                }
            }
        }

        if (!$this->getPDO()->commit()) {
            throw new Exception('Failed to commit transaction');
        }

        return $document;

    }

    /**
     * Delete Document
     *
     * @param string $collection
     * @param string $id
     *
     * @return bool
     */
    public function deleteDocument(string $collection, string $id): bool
    {
        $name = $this->filter($collection);

        $this->getPDO()->beginTransaction();

        $stmt = $this->getPDO()
            ->prepare("DELETE FROM {$this->getSQLTable($name)} WHERE _uid = :_uid");

        $stmt->bindValue(':_uid', $id, PDO::PARAM_STR);

        $stmtPermissions = $this->getPDO()->prepare("DELETE FROM {$this->getSQLTable($name.'_perms')} WHERE _document = :_uid");
        $stmtPermissions->bindValue(':_uid', $id);

        try {
            $stmt->execute() || throw new Exception('Failed to delete document');
            $stmtPermissions->execute() || throw new Exception('Failed to clean permissions');
        } catch (\Throwable $th) {
            $this->getPDO()->rollBack();
            throw new Exception($th->getMessage());
        }

        if (!$this->getPDO()->commit()) {
            throw new Exception('Failed to commit transaction');
        }

        return true;
    }

    /**
     * Find Documents
     *
     * Find data sets using chosen queries
     *
     * @param string $collection
     * @param array $queries
     * @param int $limit
     * @param int $offset
     * @param array $orderAttributes
     * @param array $orderTypes
     * @param array $cursor
     * @param string $cursorDirection
     *
     * @return array 
     * @throws Exception 
     * @throws PDOException 
     */
    public function find(string $collection, array $queries = [], int $limit = 25, int $offset = 0, array $orderAttributes = [], array $orderTypes = [], array $cursor = [], string $cursorDirection = Database::CURSOR_AFTER): array
    {
        $name = $this->filter($collection);
        $roles = Authorization::getRoles();
        $where = ['1=1'];
        $orders = [];

        $orderAttributes = \array_map(fn ($orderAttribute) => match ($orderAttribute) {
            '$id' => ID::custom('_uid'),
            '$createdAt' => '_createdAt',
            '$updatedAt' => '_updatedAt',
            default => $orderAttribute
        }, $orderAttributes);

        $hasIdAttribute = false;
        foreach ($orderAttributes as $i => $attribute) {
            if ($attribute === '_uid') {
                $hasIdAttribute = true;
            }

            $attribute = $this->filter($attribute);
            $orderType = $this->filter($orderTypes[$i] ?? Database::ORDER_ASC);

            // Get most dominant/first order attribute
            if ($i === 0 && !empty($cursor)) {
                $orderMethodInternalId = Query::TYPE_GREATER; // To preserve natural order
                $orderMethod = $orderType === Database::ORDER_DESC ? Query::TYPE_LESSER : Query::TYPE_GREATER;

                if ($cursorDirection === Database::CURSOR_BEFORE) {
                    $orderType = $orderType === Database::ORDER_ASC ? Database::ORDER_DESC : Database::ORDER_ASC;
                    $orderMethodInternalId = $orderType === Database::ORDER_ASC ? Query::TYPE_LESSER : Query::TYPE_GREATER;
                    $orderMethod = $orderType === Database::ORDER_DESC ? Query::TYPE_LESSER : Query::TYPE_GREATER;
                }

                $where[] = "(
                        table_main.\"{$attribute}\" {$this->getSQLOperator($orderMethod)} :cursor 
                        OR (
                            table_main.\"{$attribute}\" = :cursor 
                            AND
                            table_main._id {$this->getSQLOperator($orderMethodInternalId)} {$cursor['$internalId']}
                        )
                    )";
            } else if ($cursorDirection === Database::CURSOR_BEFORE) {
                $orderType = $orderType === Database::ORDER_ASC ? Database::ORDER_DESC : Database::ORDER_ASC;
            }

            $orders[] = '"'.$attribute.'" '.$orderType;
        }

        // Allow after pagination without any order
        if (empty($orderAttributes) && !empty($cursor)) {
            $orderType = $orderTypes[0] ?? Database::ORDER_ASC;
            $orderMethod = $cursorDirection === Database::CURSOR_AFTER ? ($orderType === Database::ORDER_DESC ? Query::TYPE_LESSER : Query::TYPE_GREATER
            ) : ($orderType === Database::ORDER_DESC ? Query::TYPE_GREATER : Query::TYPE_LESSER
            );
            $where[] = "( table_main._id {$this->getSQLOperator($orderMethod)} {$cursor['$internalId']} )";
        }

        // Allow order type without any order attribute, fallback to the natural order (_id)
        if (!$hasIdAttribute) {
            if (empty($orderAttributes) && !empty($orderTypes)) {
                $order = $orderTypes[0] ?? Database::ORDER_ASC;
                if ($cursorDirection === Database::CURSOR_BEFORE) {
                    $order = $order === Database::ORDER_ASC ? Database::ORDER_DESC : Database::ORDER_ASC;
                }

                $orders[] = 'table_main._id ' . $this->filter($order);
            } else {
                $orders[] = 'table_main._id ' . ($cursorDirection === Database::CURSOR_AFTER ? Database::ORDER_ASC : Database::ORDER_DESC); // Enforce last ORDER by '_id'
            }
        }

        $permissions = (Authorization::$status) ? $this->getSQLPermissionsCondition($collection, $roles) : '1=1'; // Disable join when no authorization required
        foreach ($queries as $i => $query) {
            $query->setAttribute(match ($query->getAttribute()) {
                '$id' => ID::custom('_uid'),
                '$createdAt' => '_createdAt',
                '$updatedAt' => '_updatedAt',
                default => $query->getAttribute()
            });

            $conditions = [];
            foreach ($query->getValues() as $key => $value) {
                $conditions[] = $this->getSQLCondition('"table_main"."'.$query->getAttribute().'"', $query->getMethod(), ':attribute_'.$i.'_'.$key.'_'.$query->getAttribute(), $value);
            }
            $condition = implode(' OR ', $conditions);
            $where[] = empty($condition) ? '' : '(' . $condition . ')';
        }

        $order = 'ORDER BY ' . implode(', ', $orders);

        if (Authorization::$status) {
            $where[] = $this->getSQLPermissionsCondition($name, $roles);
        }

        $sqlWhere = !empty($where) ? 'where ' . implode(' AND ', $where) : '';

        $stmt = $this->getPDO()->prepare("SELECT table_main.* FROM {$this->getSQLTable($name)} as table_main
            WHERE {$permissions} AND ".implode(' AND ', $where)."
            {$order}
            LIMIT :limit OFFSET :offset;
        ");

        foreach ($queries as $i => $query) {
            if ($query->getMethod() === Query::TYPE_SEARCH) continue;
            foreach ($query->getValues() as $key => $value) {
                $stmt->bindValue(':attribute_' . $i . '_' . $key . '_' . $query->getAttribute(), $value, $this->getPDOType($value));
            }
        }

        if (!empty($cursor) && !empty($orderAttributes) && array_key_exists(0, $orderAttributes)) {
            $attribute = $orderAttributes[0];

            $attribute = match ($attribute) {
                '_uid' => '$id',
                '_createdAt' => '$createdAt',
                '_updatedAt' => '$updatedAt',
                default => $attribute
            };

            if (is_null($cursor[$attribute] ?? null)) {
                throw new Exception("Order attribute '{$attribute}' is empty.");
            }
            $stmt->bindValue(':cursor', $cursor[$attribute], $this->getPDOType($cursor[$attribute]));
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $results = $stmt->fetchAll();

        foreach ($results as $key => $value) {
            $results[$key]['$id'] = $value['_uid'];
            $results[$key]['$internalId'] = $value['_id'];
            $results[$key]['$createdAt'] = $value['_createdAt'];
            $results[$key]['$updatedAt'] = $value['_updatedAt'];
            $results[$key]['$permissions'] = json_decode($value['_permissions'] ?? '[]', true);

            unset($results[$key]['_uid']);
            unset($results[$key]['_id']);
            unset($results[$key]['_createdAt']);
            unset($results[$key]['_updatedAt']);
            unset($results[$key]['_permissions']);

            $results[$key] = new Document($results[$key]);
        }

        if ($cursorDirection === Database::CURSOR_BEFORE) {
            $results = array_reverse($results);
        }

        return $results;
    }

    

    /**
     * Count Documents
     *
     * Count data set size using chosen queries
     *
     * @param string $collection
     * @param array $queries
     * @param int $max
     *
     * @return int
     */
    public function count(string $collection, array $queries = [], int $max = 0): int
    {
        $name = $this->filter($collection);
        $roles = Authorization::getRoles();
        $where = [];
        $limit = ($max === 0) ? '' : 'LIMIT :max';

        foreach ($queries as $i => $query) {
            $query->setAttribute(match ($query->getAttribute()) {
                '$id' => ID::custom('_uid'),
                '$createdAt' => '_createdAt',
                '$updatedAt' => '_updatedAt',
                default => $query->getAttribute()
            });

            $conditions = [];
            foreach ($query->getValues() as $key => $value) {
                $conditions[] = $this->getSQLCondition('table_main.\"' . $query->getAttribute().'\"', $query->getMethod(), ':attribute_' . $i . '_' . $key . '_' . $query->getAttribute(), $value);
            }

            $condition = implode(' OR ', $conditions);
            $where[] = empty($condition) ? '' : '(' . $condition . ')';
        }

        if (Authorization::$status) {
            $where[] = $this->getSQLPermissionsCondition($name, $roles);
        }

        $sqlWhere = !empty($where) ? 'where ' . implode(' AND ', $where) : '';
        $sql = "SELECT COUNT(1) as sum
            FROM
                (
                    SELECT 1
                    FROM {$this->getSQLTable($name)} table_main
                    " . $sqlWhere . "
                    {$limit}
                ) table_count
        ";
        $stmt = $this->getPDO()->prepare($sql);

        foreach ($queries as $i => $query) {
            if ($query->getMethod() === Query::TYPE_SEARCH) continue;
            foreach ($query->getValues() as $key => $value) {
                $stmt->bindValue(':attribute_' . $i . '_' . $key . '_' . $query->getAttribute(), $value, $this->getPDOType($value));
            }
        }

        if ($max !== 0) {
            $stmt->bindValue(':max', $max, PDO::PARAM_INT);
        }

        $stmt->execute();

        /** @var array $result */
        $result = $stmt->fetch();

        return $result['sum'] ?? 0;
    }

    /**
     * Sum an Attribute
     *
     * Sum an attribute using chosen queries
     *
     * @param string $collection
     * @param string $attribute
     * @param Query[] $queries
     * @param int $max
     *
     * @return int|float
     */
    public function sum(string $collection, string $attribute, array $queries = [], int $max = 0): int|float
    {
        $name = $this->filter($collection);
        $roles = Authorization::getRoles();
        $where = ['1=1'];
        $limit = ($max === 0) ? '' : 'LIMIT :max';

        $permissions = (Authorization::$status) ? $this->getSQLPermissionsCondition($collection, $roles) : '1=1'; // Disable join when no authorization required

        foreach($queries as $i => $query) {
            if($query->getAttribute() === '$id') {
                $query->setAttribute('_uid');
            }
            $conditions = [];
            foreach ($query->getValues() as $key => $value) {
                $conditions[] = $this->getSQLCondition('"table_main"."'.$query->getAttribute().'"', $query->getMethod(), ':attribute_'.$i.'_'.$key.'_'.$query->getAttribute(), $value);
            }

            $where[] = implode(' OR ', $conditions);
        }

        if (Authorization::$status) {
            $where[] = $this->getSQLPermissionsCondition($name, $roles);
        }

        $stmt = $this->getPDO()->prepare("SELECT SUM({$attribute}) as sum
            FROM (
                SELECT {$attribute}
                FROM {$this->getSQLTable($name)} table_main
                WHERE {$permissions} AND ".implode(' AND ', $where)."
                {$limit}
            ) table_count");

        foreach($queries as $i => $query) {
            if($query->getMethod() === Query::TYPE_SEARCH) continue;
            foreach($query->getValues() as $key => $value) {
                $stmt->bindValue(':attribute_'.$i.'_'.$key.'_'.$query->getAttribute(), $value, $this->getPDOType($value));
            }
        }

        if($max !== 0) {
            $stmt->bindValue(':max', $max, PDO::PARAM_INT);
        }

        $stmt->execute();

        /** @var array $result */
        $result = $stmt->fetch();

        return $result['sum'] ?? 0;
    }

    /**
     * Get SQL Type
     * 
     * @param string $type
     * @param int $size in chars
     * 
     * @return string
     */
    protected function getSQLType(string $type, int $size, bool $signed = true): string
    {
        switch ($type) {
            case Database::VAR_STRING:
                // $size = $size * 4; // Convert utf8mb4 size to bytes
                if($size > 16383) {
                    return 'TEXT';
                }

                return "VARCHAR({$size})";
            break;

            case Database::VAR_INTEGER:  // We don't support zerofill: https://stackoverflow.com/a/5634147/2299554

                if($size >= 8) { // INT = 4 bytes, BIGINT = 8 bytes
                    return 'BIGINT';
                }

                return 'INTEGER';
            break;

            case Database::VAR_FLOAT:
                return 'REAL';
            break;

            case Database::VAR_BOOLEAN:
                return 'BOOLEAN';
            break;

            case Database::VAR_DOCUMENT:
                return 'VARCHAR';
            break;

            case Database::VAR_DATETIME:
                return 'TIMESTAMP';
                break;

            default:
                throw new Exception('Unknown Type: '. $type);
            break;
        }
    }

    /**
     * Get SQL Condtions
     * 
     * @param string $attribute
     * @param string $operator
     * @param string $placeholder
     * @param mixed $value
     * 
     * @return string
     */
    protected function getSQLCondition(string $attribute, string $operator, string $placeholder, $value): string
    {
        switch ($operator) {
            case Query::TYPE_SEARCH:
                $value = "'".$value.":*'";
                $value = str_replace('.', ' <-> ', $value);
                return "to_tsvector(regexp_replace({$attribute}, '[^\w]+',' ','g')) @@ to_tsquery({$value})";
            break;

            default:
                return $attribute.' '.$this->getSQLOperator($operator).' '.$placeholder; // Using \"attrubute_\" to avoid conflicts with custom names;
            break;
        }
    }

    /**
     * Get SQL Index
     * 
     * @param string $collection
     * @param string $id
     * @param string $type
     * @param array $attributes
     * 
     * @return string
     */
    protected function getSQLIndex(string $collection, string $id, string $type, array $attributes): string
    {
        switch ($type) {
            case Database::INDEX_KEY:
            case Database::INDEX_ARRAY:
                $type = 'INDEX';
            break;

            case Database::INDEX_UNIQUE:
                $type = 'UNIQUE INDEX';
            break;

            case Database::INDEX_FULLTEXT:
                $type = 'INDEX';
            break;

            default:
                throw new Exception('Unknown Index Type:' . $type);
            break;
        }

        return 'CREATE '.$type.' "'.$this->getNamespace().'_'.$collection.'_'.$id.'" ON "'.$this->getDefaultDatabase().'"."'.$this->getNamespace().'_'.$collection.'" ( '.implode(', ', $attributes).' );';
    }

    /**
     * Get SQL schema
     *
     * @return string 
     */
    protected function getSQLSchema(): string
    {
        if(!$this->getSupportForSchemas()) {
            return '';
        }

        return "\"{$this->getDefaultDatabase()}\".";
    }

    /**
     * Get SQL table
     *
     * @param string $name 
     * @return string 
     */
    protected function getSQLTable(string $name): string
    {
        return "\"{$this->getDefaultDatabase()}\".\"{$this->getNamespace()}_{$name}\"";
    }

    /**
     * Get SQL Quote
     */
    protected function getSQLQuote(): string
    {
        return '"';
    }

    /**
     * Get SQL Schema Keyword
     */
    protected function getSQLSchemaKeyword(): string
    {
        return "SCHEMA";
    }

    /**
     * Get PDO Type
     * 
     * @param mixed $value
     * 
     * @return int
     */
    protected function getPDOType($value): int
    {
        switch (gettype($value)) {
            case 'string':
                return PDO::PARAM_STR;
            break;

            case 'boolean':
                return PDO::PARAM_BOOL;
            break;

            //case 'float': // (for historical reasons "double" is returned in case of a float, and not simply "float")
            case 'double':
                return PDO::PARAM_STR;
            break;

            case 'integer':
                return PDO::PARAM_INT;
            break;

            case 'NULL':
                return PDO::PARAM_NULL;
            break;

            default:
                throw new Exception('Unknown PDO Type for ' . gettype($value));
            break;
        }
    }

    /**
     * Encode array
     * 
     * @param string $value
     * 
     * @return array
     */
    protected function encodeArray(string $value): array
    {
        $string = substr($value, 1, -1);
        if (empty($string)) {
            return [];
        } else {
            return explode(',', $string);
        }
    }

    /**
     * Decode array
     * 
     * @param array $value
     * 
     * @return string
     */
    protected function decodeArray(array $value): string
    {
        if(empty($value))
            return '{}';

        foreach($value as &$item) {
            $item = '"'.str_replace(['"', '(', ')'], ['\"', '\(', '\)'], $item).'"';
        }

        return '{'.implode(",", $value).'}';
    }
}
