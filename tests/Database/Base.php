<?php

namespace Utopia\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use Utopia\Database\Database;
use Utopia\Database\Adapter;
use Utopia\Database\Validator\Authorization;

abstract class Base extends TestCase
{
    /**
     * @reture Adapter
     */
    abstract static protected function getDatabase(): Database;

    public function setUp(): void
    {
    }

    public function tearDown(): void
    {
        Authorization::reset();
    }

    public function testCreate()
    {
        $this->assertEquals(true, static::getDatabase()->create('test_database'));
    }

    public function testDelete()
    {
        $this->assertEquals(true, static::getDatabase()->delete('test_database'));
    }

    // public function testCreateCollection()
    // {
    //     $collection = self::$database->createDocument(Database::COLLECTION_COLLECTIONS, [
    //         '$collection' => Database::COLLECTION_COLLECTIONS,
    //         '$permissions' => ['read' => ['*']],
    //         'name' => 'Create',
    //     ]);

    //     $this->assertEquals(true, self::$database->createCollection($collection->getId(), [], []));
        
    //     try {
    //         self::$database->createCollection($collection->getId(), [], []);
    //     }
    //     catch (\Throwable $th) {
    //         return $this->assertEquals('42S01', $th->getCode());
    //     }

    //     throw new Exception('Expected exception');
    // }

    // public function testDeleteCollection()
    // {
    //     $collection = self::$database->createDocument(Database::COLLECTION_COLLECTIONS, [
    //         '$collection' => Database::COLLECTION_COLLECTIONS,
    //         '$permissions' => ['read' => ['*']],
    //         'name' => 'Delete',
    //     ]);

    //     $this->assertEquals(true, self::$database->createCollection($collection->getId(), [], []));
    //     $this->assertEquals(true, self::$database->deleteCollection($collection->getId()));
        
    //     try {
    //         self::$database->deleteCollection($collection->getId());
    //     }
    //     catch (\Throwable $th) {
    //         return $this->assertEquals('42S02', $th->getCode());
    //     }

    //     throw new Exception('Expected exception');
    // }

    // public function testCreateAttribute()
    // {
    //     $collection = self::$database->createDocument(Database::COLLECTION_COLLECTIONS, [
    //         '$collection' => Database::COLLECTION_COLLECTIONS,
    //         '$permissions' => ['read' => ['*']],
    //         'name' => 'Create Attribute',
    //         'rules' => [],
    //     ]);

    //     $this->assertEquals(true, self::$database->createCollection($collection->getId(), [], []));
    //     $this->assertEquals(true, self::$database->createAttribute($collection->getId(), 'title', Database::VAR_STRING));
    //     $this->assertEquals(true, self::$database->createAttribute($collection->getId(), 'description', Database::VAR_STRING));
    //     $this->assertEquals(true, self::$database->createAttribute($collection->getId(), 'numeric', Database::VAR_NUMBER));
    //     $this->assertEquals(true, self::$database->createAttribute($collection->getId(), 'integer', Database::VAR_NUMBER));
    //     $this->assertEquals(true, self::$database->createAttribute($collection->getId(), 'float', Database::VAR_NUMBER));
    //     $this->assertEquals(true, self::$database->createAttribute($collection->getId(), 'boolean', Database::VAR_BOOLEAN));
    //     $this->assertEquals(true, self::$database->createAttribute($collection->getId(), 'document', Database::VAR_DOCUMENT));
    //     $this->assertEquals(true, self::$database->createAttribute($collection->getId(), 'email', Database::VAR_STRING));
    //     $this->assertEquals(true, self::$database->createAttribute($collection->getId(), 'url', Database::VAR_STRING));
    //     $this->assertEquals(true, self::$database->createAttribute($collection->getId(), 'ipv4', Database::VAR_STRING));
    //     $this->assertEquals(true, self::$database->createAttribute($collection->getId(), 'ipv6', Database::VAR_STRING));
    //     $this->assertEquals(true, self::$database->createAttribute($collection->getId(), 'key', Database::VAR_STRING));
        
    //     // arrays
    //     $this->assertEquals(true, self::$database->createAttribute($collection->getId(), 'titles', Database::VAR_STRING, true));
    //     $this->assertEquals(true, self::$database->createAttribute($collection->getId(), 'descriptions', Database::VAR_STRING, true));
    //     $this->assertEquals(true, self::$database->createAttribute($collection->getId(), 'numerics', Database::VAR_NUMBER, true));
    //     $this->assertEquals(true, self::$database->createAttribute($collection->getId(), 'integers', Database::VAR_NUMBER, true));
    //     $this->assertEquals(true, self::$database->createAttribute($collection->getId(), 'floats', Database::VAR_NUMBER, true));
    //     $this->assertEquals(true, self::$database->createAttribute($collection->getId(), 'booleans', Database::VAR_BOOLEAN, true));
    //     $this->assertEquals(true, self::$database->createAttribute($collection->getId(), 'documents', Database::VAR_DOCUMENT, true));
    //     $this->assertEquals(true, self::$database->createAttribute($collection->getId(), 'emails', Database::VAR_STRING, true));
    //     $this->assertEquals(true, self::$database->createAttribute($collection->getId(), 'urls', Database::VAR_STRING, true));
    //     $this->assertEquals(true, self::$database->createAttribute($collection->getId(), 'ipv4s', Database::VAR_STRING, true));
    //     $this->assertEquals(true, self::$database->createAttribute($collection->getId(), 'ipv6s', Database::VAR_STRING, true));
    //     $this->assertEquals(true, self::$database->createAttribute($collection->getId(), 'keys', Database::VAR_STRING, true));
    // }

    // public function testDeleteAttribute()
    // {
    //     $collection = self::$database->createDocument(Database::COLLECTION_COLLECTIONS, [
    //         '$collection' => Database::COLLECTION_COLLECTIONS,
    //         '$permissions' => ['read' => ['*']],
    //         'name' => 'Delete Attribute',
    //         'rules' => [],
    //     ]);

    //     $this->assertEquals(true, self::$database->createCollection($collection->getId(), [], []));
        
    //     $this->assertEquals(true, self::$database->createAttribute($collection->getId(), 'title', Database::VAR_STRING));
    //     $this->assertEquals(true, self::$database->createAttribute($collection->getId(), 'description', Database::VAR_STRING));
    //     $this->assertEquals(true, self::$database->createAttribute($collection->getId(), 'value', Database::VAR_NUMBER));

    //     $this->assertEquals(true, self::$database->deleteAttribute($collection->getId(), 'title', false));
    //     $this->assertEquals(true, self::$database->deleteAttribute($collection->getId(), 'description', false));
    //     $this->assertEquals(true, self::$database->deleteAttribute($collection->getId(), 'value', false));

    //     $this->assertEquals(true, self::$database->createAttribute($collection->getId(), 'titles', Database::VAR_STRING, true));
    //     $this->assertEquals(true, self::$database->createAttribute($collection->getId(), 'descriptions', Database::VAR_STRING, true));
    //     $this->assertEquals(true, self::$database->createAttribute($collection->getId(), 'values', Database::VAR_NUMBER, true));

    //     $this->assertEquals(true, self::$database->deleteAttribute($collection->getId(), 'titles', true));
    //     $this->assertEquals(true, self::$database->deleteAttribute($collection->getId(), 'descriptions', true));
    //     $this->assertEquals(true, self::$database->deleteAttribute($collection->getId(), 'values', true));
    // }

    // public function testCreateIndex()
    // {
    //     $collection = self::$database->createDocument(Database::COLLECTION_COLLECTIONS, [
    //         '$collection' => Database::COLLECTION_COLLECTIONS,
    //         '$permissions' => ['read' => ['*']],
    //         'name' => 'Create Index',
    //         'rules' => [],
    //     ]);

    //     $this->assertEquals(true, self::$database->createCollection($collection->getId(), [], []));
    //     $this->assertEquals(true, self::$database->createAttribute($collection->getId(), 'title', Database::VAR_STRING));
    //     $this->assertEquals(true, self::$database->createAttribute($collection->getId(), 'description', Database::VAR_STRING));
    //     $this->assertEquals(true, self::$database->createIndex($collection->getId(), 'x', Database::INDEX_KEY, ['title']));
    //     $this->assertEquals(true, self::$database->createIndex($collection->getId(), 'y', Database::INDEX_KEY, ['description']));
    //     $this->assertEquals(true, self::$database->createIndex($collection->getId(), 'z', Database::INDEX_KEY, ['title', 'description']));
    // }

    // public function testDeleteIndex()
    // {
    //     $collection = self::$database->createDocument(Database::COLLECTION_COLLECTIONS, [
    //         '$collection' => Database::COLLECTION_COLLECTIONS,
    //         '$permissions' => ['read' => ['*']],
    //         'name' => 'Delete Index',
    //         'rules' => [],
    //     ]);

    //     $this->assertEquals(true, self::$database->createCollection($collection->getId(), [], []));
    //     $this->assertEquals(true, self::$database->createAttribute($collection->getId(), 'title', Database::VAR_STRING));
    //     $this->assertEquals(true, self::$database->createAttribute($collection->getId(), 'description', Database::VAR_STRING));
    //     $this->assertEquals(true, self::$database->createIndex($collection->getId(), 'x', Database::INDEX_KEY, ['title']));
    //     $this->assertEquals(true, self::$database->createIndex($collection->getId(), 'y', Database::INDEX_KEY, ['description']));
    //     $this->assertEquals(true, self::$database->createIndex($collection->getId(), 'z', Database::INDEX_KEY, ['title', 'description']));
        
    //     $this->assertEquals(true, self::$database->deleteIndex($collection->getId(), 'x'));
    //     $this->assertEquals(true, self::$database->deleteIndex($collection->getId(), 'y'));
    //     $this->assertEquals(true, self::$database->deleteIndex($collection->getId(), 'z'));
    // }

    // public function testCreateDocument()
    // {
    //     $collection1 = self::$database->createDocument(Database::COLLECTION_COLLECTIONS, [
    //         '$collection' => Database::COLLECTION_COLLECTIONS,
    //         '$permissions' => ['read' => ['*']],
    //         'name' => 'Create Documents',
    //         'rules' => [
    //             [
    //                 '$collection' => Database::COLLECTION_RULES,
    //                 '$permissions' => ['read' => ['*']],
    //                 'label' => 'Name',
    //                 'key' => 'name',
    //                 'type' => Database::VAR_STRING,
    //                 'default' => '',
    //                 'required' => true,
    //                 'array' => false,
    //             ],
    //             [
    //                 '$collection' => Database::COLLECTION_RULES,
    //                 '$permissions' => ['read' => ['*']],
    //                 'label' => 'Links',
    //                 'key' => 'links',
    //                 'type' => Database::VAR_STRING,
    //                 'default' => '',
    //                 'required' => true,
    //                 'array' => true,
    //             ],
    //         ]
    //     ]);

    //     $this->assertEquals(true, self::$database->createCollection($collection1->getId(), [], []));
        
    //     $document0 = new Document([
    //         '$collection' => $collection1->getId(),
    //         '$permissions' => [
    //             'read' => ['*'],
    //             'write' => ['user:123'],
    //         ],
    //         'name' => 'Task #0',
    //         'links' => [
    //             'http://example.com/link-1',
    //             'http://example.com/link-2',
    //             'http://example.com/link-3',
    //             'http://example.com/link-4',
    //         ],
    //     ]);
        
    //     $document1 = self::$database->createDocument($collection1->getId(), [
    //         '$collection' => $collection1->getId(),
    //         '$permissions' => [
    //             'read' => ['*'],
    //             'write' => ['user:123'],
    //         ],
    //         'name' => 'Task #1️⃣',
    //         'links' => [
    //             'http://example.com/link-5',
    //             'http://example.com/link-6',
    //             'http://example.com/link-7',
    //             'http://example.com/link-8',
    //         ],
    //     ]);

    //     $this->assertNotEmpty($document1->getId());
    //     $this->assertIsArray($document1->getPermissions());
    //     $this->assertArrayHasKey('read', $document1->getPermissions());
    //     $this->assertArrayHasKey('write', $document1->getPermissions());
    //     $this->assertEquals('Task #1️⃣', $document1->getAttribute('name'));
    //     $this->assertCount(4, $document1->getAttribute('links'));
    //     $this->assertEquals('http://example.com/link-5', $document1->getAttribute('links')[0]);
    //     $this->assertEquals('http://example.com/link-6', $document1->getAttribute('links')[1]);
    //     $this->assertEquals('http://example.com/link-7', $document1->getAttribute('links')[2]);
    //     $this->assertEquals('http://example.com/link-8', $document1->getAttribute('links')[3]);

    //     $document2 = self::$database->createDocument(Database::COLLECTION_USERS, [
    //         '$collection' => Database::COLLECTION_USERS,
    //         '$permissions' => [
    //             'read' => ['*'],
    //             'write' => ['user:123'],
    //         ],
    //         'email' => 'test@appwrite.io',
    //         'emailVerification' => false,
    //         'status' => 0,
    //         'password' => 'secrethash',
    //         'password-update' => \time(),
    //         'registration' => \time(),
    //         'reset' => false,
    //         'name' => 'Test',
    //     ]);

    //     $this->assertNotEmpty($document2->getId());
    //     $this->assertIsArray($document2->getPermissions());
    //     $this->assertArrayHasKey('read', $document2->getPermissions());
    //     $this->assertArrayHasKey('write', $document2->getPermissions());
    //     $this->assertEquals('test@appwrite.io', $document2->getAttribute('email'));
    //     $this->assertIsString($document2->getAttribute('email'));
    //     $this->assertEquals(0, $document2->getAttribute('status'));
    //     $this->assertIsInt($document2->getAttribute('status'));
    //     $this->assertEquals(false, $document2->getAttribute('emailVerification'));
    //     $this->assertIsBool($document2->getAttribute('emailVerification'));

    //     $document2 = self::$database->getDocument(Database::COLLECTION_USERS, $document2->getId());

    //     $this->assertNotEmpty($document2->getId());
    //     $this->assertIsArray($document2->getPermissions());
    //     $this->assertArrayHasKey('read', $document2->getPermissions());
    //     $this->assertArrayHasKey('write', $document2->getPermissions());
    //     $this->assertEquals('test@appwrite.io', $document2->getAttribute('email'));
    //     $this->assertIsString($document2->getAttribute('email'));
    //     $this->assertEquals(0, $document2->getAttribute('status'));
    //     $this->assertIsInt($document2->getAttribute('status'));
    //     $this->assertEquals(false, $document2->getAttribute('emailVerification'));
    //     $this->assertIsBool($document2->getAttribute('emailVerification'));

    //     $types = [
    //         Database::VAR_STRING,
    //         Database::VAR_NUMBER,
    //         Database::VAR_BOOLEAN,
    //         Database::VAR_DOCUMENT,
    //     ];

    //     $rules = [];

    //     foreach($types as $type) {
    //         $rules[] = [
    //             '$collection' => Database::COLLECTION_RULES,
    //             '$permissions' => ['read' => ['*']],
    //             'label' => ucfirst($type),
    //             'key' => $type,
    //             'type' => $type,
    //             'default' => null,
    //             'required' => true,
    //             'array' => false,
    //             'list' => ($type === Database::VAR_DOCUMENT) ? [$collection1->getId()] : [],
    //         ];

    //         $rules[] = [
    //             '$collection' => Database::COLLECTION_RULES,
    //             '$permissions' => ['read' => ['*']],
    //             'label' => ucfirst($type),
    //             'key' => $type.'s',
    //             'type' => $type,
    //             'default' => null,
    //             'required' => true,
    //             'array' => true,
    //             'list' => ($type === Database::VAR_DOCUMENT) ? [$collection1->getId()] : [],
    //         ];
    //     }

    //     $rules[] = [
    //         '$collection' => Database::COLLECTION_RULES,
    //         '$permissions' => ['read' => ['*']],
    //         'label' => 'document2',
    //         'key' => 'document2',
    //         'type' => Database::VAR_DOCUMENT,
    //         'default' => null,
    //         'required' => true,
    //         'array' => false,
    //         'list' => [$collection1->getId()],
    //     ];

    //     $rules[] = [
    //         '$collection' => Database::COLLECTION_RULES,
    //         '$permissions' => ['read' => ['*']],
    //         'label' => 'documents2',
    //         'key' => 'documents2',
    //         'type' => Database::VAR_DOCUMENT,
    //         'default' => null,
    //         'required' => true,
    //         'array' => true,
    //         'list' => [$collection1->getId()],
    //     ];

    //     $collection2 = self::$database->createDocument(Database::COLLECTION_COLLECTIONS, [
    //         '$collection' => Database::COLLECTION_COLLECTIONS,
    //         '$permissions' => ['read' => ['*']],
    //         'name' => 'Create Documents',
    //         'rules' => $rules,
    //     ]);

    //     $this->assertEquals(true, self::$database->createCollection($collection2->getId(), [], []));
        
    //     $document3 = self::$database->createDocument($collection2->getId(), [
    //         '$collection' => $collection2->getId(),
    //         '$permissions' => [
    //             'read' => ['*'],
    //             'write' => ['user:123'],
    //         ],
    //         'text' => 'Hello World',
    //         'texts' => ['Hello World 1', 'Hello World 2'],
    //         // 'document' => $document0,
    //         // 'documents' => [$document0],
    //         'document' => $document0,
    //         'documents' => [$document1, $document0],
    //         'document2' => $document1,
    //         'documents2' => [$document0, $document1],
    //         'integer' => 1,
    //         'integers' => [5, 3, 4],
    //         'float' => 2.22,
    //         'floats' => [1.13, 4.33, 8.9999],
    //         'numeric' => 1,
    //         'numerics' => [1, 5, 7.77],
    //         'boolean' => true,
    //         'booleans' => [true, false, true],
    //         'email' => 'test@appwrite.io',
    //         'emails' => [
    //             'test4@appwrite.io',
    //             'test3@appwrite.io',
    //             'test2@appwrite.io',
    //             'test1@appwrite.io'
    //         ],
    //         'url' => 'http://example.com/welcome',
    //         'urls' => [
    //             'http://example.com/welcome-1',
    //             'http://example.com/welcome-2',
    //             'http://example.com/welcome-3'
    //         ],
    //         'ipv4' => '172.16.254.1',
    //         'ipv4s' => [
    //             '172.16.254.1',
    //             '172.16.254.5'
    //         ],
    //         'ipv6' => '2001:0db8:85a3:0000:0000:8a2e:0370:7334',
    //         'ipv6s' => [
    //             '2001:0db8:85a3:0000:0000:8a2e:0370:7334',
    //             '2001:0db8:85a3:0000:0000:8a2e:0370:7337'
    //         ],
    //         'key' => uniqid(),
    //         'keys' => [uniqid(), uniqid(), uniqid()],
    //     ]);

    //     $document3 = self::$database->getDocument($collection2->getId(), $document3->getId());

    //     $this->assertIsString($document3->getId());
    //     $this->assertIsString($document3->getCollection());
    //     $this->assertEquals([
    //         'read' => ['*'],
    //         'write' => ['user:123'],
    //     ], $document3->getPermissions());
    //     $this->assertEquals('Hello World', $document3->getAttribute('text'));
    //     $this->assertCount(2, $document3->getAttribute('texts'));
        
    //     $this->assertIsString($document3->getAttribute('text'));
    //     $this->assertEquals('Hello World', $document3->getAttribute('text'));
    //     $this->assertEquals(['Hello World 1', 'Hello World 2'], $document3->getAttribute('texts'));
    //     $this->assertCount(2, $document3->getAttribute('texts'));
        
    //     $this->assertInstanceOf(Document::class, $document3->getAttribute('document'));
    //     $this->assertIsString($document3->getAttribute('document')->getId());
    //     $this->assertNotEmpty($document3->getAttribute('document')->getId());
    //     $this->assertIsArray($document3->getAttribute('document')->getPermissions());
    //     $this->assertArrayHasKey('read', $document3->getAttribute('document')->getPermissions());
    //     $this->assertArrayHasKey('write', $document3->getAttribute('document')->getPermissions());
    //     $this->assertEquals('Task #0', $document3->getAttribute('document')->getAttribute('name'));
    //     $this->assertCount(4, $document3->getAttribute('document')->getAttribute('links'));
    //     $this->assertEquals('http://example.com/link-1', $document3->getAttribute('document')->getAttribute('links')[0]);
    //     $this->assertEquals('http://example.com/link-2', $document3->getAttribute('document')->getAttribute('links')[1]);
    //     $this->assertEquals('http://example.com/link-3', $document3->getAttribute('document')->getAttribute('links')[2]);
    //     $this->assertEquals('http://example.com/link-4', $document3->getAttribute('document')->getAttribute('links')[3]);
        
    //     $this->assertInstanceOf(Document::class, $document3->getAttribute('documents')[0]);
    //     $this->assertIsString($document3->getAttribute('documents')[0]->getId());
    //     $this->assertNotEmpty($document3->getAttribute('documents')[0]->getId());
    //     $this->assertIsArray($document3->getAttribute('documents')[0]->getPermissions());
    //     $this->assertArrayHasKey('read', $document3->getAttribute('documents')[0]->getPermissions());
    //     $this->assertArrayHasKey('write', $document3->getAttribute('documents')[0]->getPermissions());
    //     $this->assertEquals('Task #1️⃣', $document3->getAttribute('documents')[0]->getAttribute('name'));
    //     $this->assertCount(4, $document3->getAttribute('documents')[0]->getAttribute('links'));
    //     $this->assertEquals('http://example.com/link-5', $document3->getAttribute('documents')[0]->getAttribute('links')[0]);
    //     $this->assertEquals('http://example.com/link-6', $document3->getAttribute('documents')[0]->getAttribute('links')[1]);
    //     $this->assertEquals('http://example.com/link-7', $document3->getAttribute('documents')[0]->getAttribute('links')[2]);
    //     $this->assertEquals('http://example.com/link-8', $document3->getAttribute('documents')[0]->getAttribute('links')[3]);
        
    //     $this->assertInstanceOf(Document::class, $document3->getAttribute('documents')[1]);
    //     $this->assertIsString($document3->getAttribute('documents')[1]->getId());
    //     $this->assertNotEmpty($document3->getAttribute('documents')[1]->getId());
    //     $this->assertIsArray($document3->getAttribute('documents')[1]->getPermissions());
    //     $this->assertArrayHasKey('read', $document3->getAttribute('documents')[1]->getPermissions());
    //     $this->assertArrayHasKey('write', $document3->getAttribute('documents')[1]->getPermissions());
    //     $this->assertEquals('Task #0', $document3->getAttribute('documents')[1]->getAttribute('name'));
    //     $this->assertCount(4, $document3->getAttribute('documents')[1]->getAttribute('links'));
    //     $this->assertEquals('http://example.com/link-1', $document3->getAttribute('documents')[1]->getAttribute('links')[0]);
    //     $this->assertEquals('http://example.com/link-2', $document3->getAttribute('documents')[1]->getAttribute('links')[1]);
    //     $this->assertEquals('http://example.com/link-3', $document3->getAttribute('documents')[1]->getAttribute('links')[2]);
    //     $this->assertEquals('http://example.com/link-4', $document3->getAttribute('documents')[1]->getAttribute('links')[3]);

    //     $this->assertInstanceOf(Document::class, $document3->getAttribute('document2'));
    //     $this->assertIsString($document3->getAttribute('document2')->getId());
    //     $this->assertNotEmpty($document3->getAttribute('document2')->getId());
    //     $this->assertIsArray($document3->getAttribute('document2')->getPermissions());
    //     $this->assertArrayHasKey('read', $document3->getAttribute('document2')->getPermissions());
    //     $this->assertArrayHasKey('write', $document3->getAttribute('document2')->getPermissions());
    //     $this->assertEquals('Task #1️⃣', $document3->getAttribute('document2')->getAttribute('name'));
    //     $this->assertCount(4, $document3->getAttribute('document2')->getAttribute('links'));
    //     $this->assertEquals('http://example.com/link-5', $document3->getAttribute('document2')->getAttribute('links')[0]);
    //     $this->assertEquals('http://example.com/link-6', $document3->getAttribute('document2')->getAttribute('links')[1]);
    //     $this->assertEquals('http://example.com/link-7', $document3->getAttribute('document2')->getAttribute('links')[2]);
    //     $this->assertEquals('http://example.com/link-8', $document3->getAttribute('document2')->getAttribute('links')[3]);

    //     $this->assertInstanceOf(Document::class, $document3->getAttribute('documents2')[0]);
    //     $this->assertIsString($document3->getAttribute('documents2')[0]->getId());
    //     $this->assertNotEmpty($document3->getAttribute('documents2')[0]->getId());
    //     $this->assertIsArray($document3->getAttribute('documents2')[0]->getPermissions());
    //     $this->assertArrayHasKey('read', $document3->getAttribute('documents2')[0]->getPermissions());
    //     $this->assertArrayHasKey('write', $document3->getAttribute('documents2')[0]->getPermissions());
    //     $this->assertEquals('Task #0', $document3->getAttribute('documents2')[0]->getAttribute('name'));
    //     $this->assertCount(4, $document3->getAttribute('documents2')[0]->getAttribute('links'));
    //     $this->assertEquals('http://example.com/link-1', $document3->getAttribute('documents2')[0]->getAttribute('links')[0]);
    //     $this->assertEquals('http://example.com/link-2', $document3->getAttribute('documents2')[0]->getAttribute('links')[1]);
    //     $this->assertEquals('http://example.com/link-3', $document3->getAttribute('documents2')[0]->getAttribute('links')[2]);
    //     $this->assertEquals('http://example.com/link-4', $document3->getAttribute('documents2')[0]->getAttribute('links')[3]);

    //     $this->assertInstanceOf(Document::class, $document3->getAttribute('documents2')[1]);
    //     $this->assertIsString($document3->getAttribute('documents2')[1]->getId());
    //     $this->assertNotEmpty($document3->getAttribute('documents2')[1]->getId());
    //     $this->assertIsArray($document3->getAttribute('documents2')[1]->getPermissions());
    //     $this->assertArrayHasKey('read', $document3->getAttribute('documents2')[1]->getPermissions());
    //     $this->assertArrayHasKey('write', $document3->getAttribute('documents2')[1]->getPermissions());
    //     $this->assertEquals('Task #1️⃣', $document3->getAttribute('documents2')[1]->getAttribute('name'));
    //     $this->assertCount(4, $document3->getAttribute('documents2')[1]->getAttribute('links'));
    //     $this->assertEquals('http://example.com/link-5', $document3->getAttribute('documents2')[1]->getAttribute('links')[0]);
    //     $this->assertEquals('http://example.com/link-6', $document3->getAttribute('documents2')[1]->getAttribute('links')[1]);
    //     $this->assertEquals('http://example.com/link-7', $document3->getAttribute('documents2')[1]->getAttribute('links')[2]);
    //     $this->assertEquals('http://example.com/link-8', $document3->getAttribute('documents2')[1]->getAttribute('links')[3]);
        
    //     $this->assertIsInt($document3->getAttribute('integer'));
    //     $this->assertEquals(1, $document3->getAttribute('integer'));
    //     $this->assertIsInt($document3->getAttribute('integers')[0]);
    //     $this->assertIsInt($document3->getAttribute('integers')[1]);
    //     $this->assertIsInt($document3->getAttribute('integers')[2]);
    //     $this->assertEquals([5, 3, 4], $document3->getAttribute('integers'));
    //     $this->assertCount(3, $document3->getAttribute('integers'));

    //     $this->assertIsFloat($document3->getAttribute('float'));
    //     $this->assertEquals(2.22, $document3->getAttribute('float'));
    //     $this->assertIsFloat($document3->getAttribute('floats')[0]);
    //     $this->assertIsFloat($document3->getAttribute('floats')[1]);
    //     $this->assertIsFloat($document3->getAttribute('floats')[2]);
    //     $this->assertEquals([1.13, 4.33, 8.9999], $document3->getAttribute('floats'));
    //     $this->assertCount(3, $document3->getAttribute('floats'));

    //     $this->assertIsBool($document3->getAttribute('boolean'));
    //     $this->assertEquals(true, $document3->getAttribute('boolean'));
    //     $this->assertIsBool($document3->getAttribute('booleans')[0]);
    //     $this->assertIsBool($document3->getAttribute('booleans')[1]);
    //     $this->assertIsBool($document3->getAttribute('booleans')[2]);
    //     $this->assertEquals([true, false, true], $document3->getAttribute('booleans'));
    //     $this->assertCount(3, $document3->getAttribute('booleans'));

    //     $this->assertIsString($document3->getAttribute('email'));
    //     $this->assertEquals('test@appwrite.io', $document3->getAttribute('email'));
    //     $this->assertIsString($document3->getAttribute('emails')[0]);
    //     $this->assertIsString($document3->getAttribute('emails')[1]);
    //     $this->assertIsString($document3->getAttribute('emails')[2]);
    //     $this->assertIsString($document3->getAttribute('emails')[3]);
    //     $this->assertEquals([
    //         'test4@appwrite.io',
    //         'test3@appwrite.io',
    //         'test2@appwrite.io',
    //         'test1@appwrite.io'
    //     ], $document3->getAttribute('emails'));
    //     $this->assertCount(4, $document3->getAttribute('emails'));

    //     $this->assertIsString($document3->getAttribute('url'));
    //     $this->assertEquals('http://example.com/welcome', $document3->getAttribute('url'));
    //     $this->assertIsString($document3->getAttribute('urls')[0]);
    //     $this->assertIsString($document3->getAttribute('urls')[1]);
    //     $this->assertIsString($document3->getAttribute('urls')[2]);
    //     $this->assertEquals([
    //         'http://example.com/welcome-1',
    //         'http://example.com/welcome-2',
    //         'http://example.com/welcome-3'
    //     ], $document3->getAttribute('urls'));
    //     $this->assertCount(3, $document3->getAttribute('urls'));

    //     $this->assertIsString($document3->getAttribute('ipv4'));
    //     $this->assertEquals('172.16.254.1', $document3->getAttribute('ipv4'));
    //     $this->assertIsString($document3->getAttribute('ipv4s')[0]);
    //     $this->assertIsString($document3->getAttribute('ipv4s')[1]);
    //     $this->assertEquals([
    //         '172.16.254.1',
    //         '172.16.254.5'
    //     ], $document3->getAttribute('ipv4s'));
    //     $this->assertCount(2, $document3->getAttribute('ipv4s'));

    //     $this->assertIsString($document3->getAttribute('ipv6'));
    //     $this->assertEquals('2001:0db8:85a3:0000:0000:8a2e:0370:7334', $document3->getAttribute('ipv6'));
    //     $this->assertIsString($document3->getAttribute('ipv6s')[0]);
    //     $this->assertIsString($document3->getAttribute('ipv6s')[1]);
    //     $this->assertEquals([
    //         '2001:0db8:85a3:0000:0000:8a2e:0370:7334',
    //         '2001:0db8:85a3:0000:0000:8a2e:0370:7337'
    //     ], $document3->getAttribute('ipv6s'));
    //     $this->assertCount(2, $document3->getAttribute('ipv6s'));

    //     $this->assertEquals('2001:0db8:85a3:0000:0000:8a2e:0370:7334', $document3->getAttribute('ipv6'));
    //     $this->assertEquals([
    //         '2001:0db8:85a3:0000:0000:8a2e:0370:7334',
    //         '2001:0db8:85a3:0000:0000:8a2e:0370:7337'
    //     ], $document3->getAttribute('ipv6s'));
    //     $this->assertCount(2, $document3->getAttribute('ipv6s'));

    //     $this->assertIsString($document3->getAttribute('key'));
    //     $this->assertCount(3, $document3->getAttribute('keys'));
    // }

    // public function testGetDocument()
    // {
    //     // Mocked document
    //     $document = self::$database->getDocument(Database::COLLECTION_COLLECTIONS, Database::COLLECTION_USERS);

    //     $this->assertEquals(Database::COLLECTION_USERS, $document->getId());
    //     $this->assertEquals(Database::COLLECTION_COLLECTIONS, $document->getCollection());

    //     $collection1 = self::$database->createDocument(Database::COLLECTION_COLLECTIONS, [
    //         '$collection' => Database::COLLECTION_COLLECTIONS,
    //         '$permissions' => ['read' => ['*']],
    //         'name' => 'Create Documents',
    //         'rules' => [
    //             [
    //                 '$collection' => Database::COLLECTION_RULES,
    //                 '$permissions' => ['read' => ['*']],
    //                 'label' => 'Name',
    //                 'key' => 'name',
    //                 'type' => Database::VAR_STRING,
    //                 'default' => '',
    //                 'required' => true,
    //                 'array' => false,
    //             ],
    //             [
    //                 '$collection' => Database::COLLECTION_RULES,
    //                 '$permissions' => ['read' => ['*']],
    //                 'label' => 'Links',
    //                 'key' => 'links',
    //                 'type' => Database::VAR_STRING,
    //                 'default' => '',
    //                 'required' => true,
    //                 'array' => true,
    //             ],
    //         ]
    //     ]);

    //     $this->assertEquals(true, self::$database->createCollection($collection1->getId(), [], []));
        
    //     $document1 = self::$database->createDocument($collection1->getId(), [
    //         '$collection' => $collection1->getId(),
    //         '$permissions' => [
    //             'read' => ['*'],
    //             'write' => ['user:123'],
    //         ],
    //         'name' => 'Task #1️⃣',
    //         'links' => [
    //             'http://example.com/link-5',
    //             'http://example.com/link-6',
    //             'http://example.com/link-7',
    //             'http://example.com/link-8',
    //         ],
    //     ]);

    //     $document1 = self::$database->getDocument($collection1->getId(), $document1->getId());

    //     $this->assertFalse($document1->isEmpty());
    //     $this->assertNotEmpty($document1->getId());
    //     $this->assertIsArray($document1->getPermissions());
    //     $this->assertArrayHasKey('read', $document1->getPermissions());
    //     $this->assertArrayHasKey('write', $document1->getPermissions());
    //     $this->assertEquals('Task #1️⃣', $document1->getAttribute('name'));
    //     $this->assertCount(4, $document1->getAttribute('links'));
    //     $this->assertEquals('http://example.com/link-5', $document1->getAttribute('links')[0]);
    //     $this->assertEquals('http://example.com/link-6', $document1->getAttribute('links')[1]);
    //     $this->assertEquals('http://example.com/link-7', $document1->getAttribute('links')[2]);
    //     $this->assertEquals('http://example.com/link-8', $document1->getAttribute('links')[3]);

    //     $document1 = self::$database->getDocument($collection1->getId(), $document1->getId().'x');

    //     $this->assertTrue($document1->isEmpty());
    //     $this->assertEmpty($document1->getId());
    //     $this->assertEmpty($document1->getCollection());
    //     $this->assertIsArray($document1->getPermissions());
    //     $this->assertEmpty($document1->getPermissions());
    // }

    // public function testUpdateDocument()
    // {
    //     $collection1 = self::$database->createDocument(Database::COLLECTION_COLLECTIONS, [
    //         '$collection' => Database::COLLECTION_COLLECTIONS,
    //         '$permissions' => ['read' => ['*']],
    //         'name' => 'Create Documents',
    //         'rules' => [
    //             [
    //                 '$collection' => Database::COLLECTION_RULES,
    //                 '$permissions' => ['read' => ['*']],
    //                 'label' => 'Name',
    //                 'key' => 'name',
    //                 'type' => Database::VAR_STRING,
    //                 'default' => '',
    //                 'required' => true,
    //                 'array' => false,
    //             ],
    //             [
    //                 '$collection' => Database::COLLECTION_RULES,
    //                 '$permissions' => ['read' => ['*']],
    //                 'label' => 'Links',
    //                 'key' => 'links',
    //                 'type' => Database::VAR_STRING,
    //                 'default' => '',
    //                 'required' => true,
    //                 'array' => true,
    //             ],
    //         ]
    //     ]);

    //     $this->assertEquals(true, self::$database->createCollection($collection1->getId(), [], []));
        
    //     $document0 = new Document([
    //         '$collection' => $collection1->getId(),
    //         '$permissions' => [
    //             'read' => ['*'],
    //             'write' => ['user:123'],
    //         ],
    //         'name' => 'Task #0',
    //         'links' => [
    //             'http://example.com/link-1',
    //             'http://example.com/link-2',
    //             'http://example.com/link-3',
    //             'http://example.com/link-4',
    //         ],
    //     ]);
        
    //     $document1 = self::$database->createDocument($collection1->getId(), [
    //         '$collection' => $collection1->getId(),
    //         '$permissions' => [
    //             'read' => ['*'],
    //             'write' => ['user:123'],
    //         ],
    //         'name' => 'Task #1️⃣',
    //         'links' => [
    //             'http://example.com/link-5',
    //             'http://example.com/link-6',
    //             'http://example.com/link-7',
    //             'http://example.com/link-8',
    //         ],
    //     ]);

    //     $this->assertNotEmpty($document1->getId());
    //     $this->assertIsArray($document1->getPermissions());
    //     $this->assertArrayHasKey('read', $document1->getPermissions());
    //     $this->assertArrayHasKey('write', $document1->getPermissions());
    //     $this->assertEquals('Task #1️⃣', $document1->getAttribute('name'));
    //     $this->assertCount(4, $document1->getAttribute('links'));
    //     $this->assertEquals('http://example.com/link-5', $document1->getAttribute('links')[0]);
    //     $this->assertEquals('http://example.com/link-6', $document1->getAttribute('links')[1]);
    //     $this->assertEquals('http://example.com/link-7', $document1->getAttribute('links')[2]);
    //     $this->assertEquals('http://example.com/link-8', $document1->getAttribute('links')[3]);
        
    //     $document1 = self::$database->getDocument($collection1->getId(), $document1->getId());

    //     $this->assertNotEmpty($document1->getId());
    //     $this->assertIsArray($document1->getPermissions());
    //     $this->assertArrayHasKey('read', $document1->getPermissions());
    //     $this->assertArrayHasKey('write', $document1->getPermissions());
    //     $this->assertEquals('Task #1️⃣', $document1->getAttribute('name'));
    //     $this->assertCount(4, $document1->getAttribute('links'));
    //     $this->assertEquals('http://example.com/link-5', $document1->getAttribute('links')[0]);
    //     $this->assertEquals('http://example.com/link-6', $document1->getAttribute('links')[1]);
    //     $this->assertEquals('http://example.com/link-7', $document1->getAttribute('links')[2]);
    //     $this->assertEquals('http://example.com/link-8', $document1->getAttribute('links')[3]);

    //     $document1 = self::$database->updateDocument($collection1->getId(), $document1->getId(), [
    //         '$id' => $document1->getId(),
    //         '$collection' => $collection1->getId(),
    //         '$permissions' => [
    //             'read' => ['user:1234'],
    //             'write' => ['user:1234'],
    //         ],
    //         'name' => 'Task #1x',
    //         'links' => [
    //             'http://example.com/link-5x',
    //             'http://example.com/link-6x',
    //             'http://example.com/link-7x',
    //             'http://example.com/link-8x',
    //         ],
    //     ]);

    //     $this->assertNotEmpty($document1->getId());
    //     $this->assertIsArray($document1->getPermissions());
    //     $this->assertArrayHasKey('read', $document1->getPermissions());
    //     $this->assertArrayHasKey('write', $document1->getPermissions());
    //     $this->assertEquals('Task #1x', $document1->getAttribute('name'));
    //     $this->assertCount(4, $document1->getAttribute('links'));
    //     $this->assertEquals('http://example.com/link-5x', $document1->getAttribute('links')[0]);
    //     $this->assertEquals('http://example.com/link-6x', $document1->getAttribute('links')[1]);
    //     $this->assertEquals('http://example.com/link-7x', $document1->getAttribute('links')[2]);
    //     $this->assertEquals('http://example.com/link-8x', $document1->getAttribute('links')[3]);

    //     $document1 = self::$database->getDocument($collection1->getId(), $document1->getId());

    //     $this->assertNotEmpty($document1->getId());
    //     $this->assertIsArray($document1->getPermissions());
    //     $this->assertArrayHasKey('read', $document1->getPermissions());
    //     $this->assertArrayHasKey('write', $document1->getPermissions());
    //     $this->assertEquals('Task #1x', $document1->getAttribute('name'));
    //     $this->assertCount(4, $document1->getAttribute('links'));
    //     $this->assertEquals('http://example.com/link-5x', $document1->getAttribute('links')[0]);
    //     $this->assertEquals('http://example.com/link-6x', $document1->getAttribute('links')[1]);
    //     $this->assertEquals('http://example.com/link-7x', $document1->getAttribute('links')[2]);
    //     $this->assertEquals('http://example.com/link-8x', $document1->getAttribute('links')[3]);

    //     $document2 = self::$database->createDocument(Database::COLLECTION_USERS, [
    //         '$collection' => Database::COLLECTION_USERS,
    //         '$permissions' => [
    //             'read' => ['*'],
    //             'write' => ['user:123'],
    //         ],
    //         'email' => 'test5@appwrite.io',
    //         'emailVerification' => false,
    //         'status' => 0,
    //         'password' => 'secrethash',
    //         'password-update' => \time(),
    //         'registration' => \time(),
    //         'reset' => false,
    //         'name' => 'Test',
    //     ]);

    //     $this->assertNotEmpty($document2->getId());
    //     $this->assertIsArray($document2->getPermissions());
    //     $this->assertArrayHasKey('read', $document2->getPermissions());
    //     $this->assertArrayHasKey('write', $document2->getPermissions());
    //     $this->assertEquals('test5@appwrite.io', $document2->getAttribute('email'));
    //     $this->assertIsString($document2->getAttribute('email'));
    //     $this->assertEquals(0, $document2->getAttribute('status'));
    //     $this->assertIsInt($document2->getAttribute('status'));
    //     $this->assertEquals(false, $document2->getAttribute('emailVerification'));
    //     $this->assertIsBool($document2->getAttribute('emailVerification'));

    //     $document2 = self::$database->getDocument(Database::COLLECTION_USERS, $document2->getId());

    //     $this->assertNotEmpty($document2->getId());
    //     $this->assertIsArray($document2->getPermissions());
    //     $this->assertArrayHasKey('read', $document2->getPermissions());
    //     $this->assertArrayHasKey('write', $document2->getPermissions());
    //     $this->assertEquals('test5@appwrite.io', $document2->getAttribute('email'));
    //     $this->assertIsString($document2->getAttribute('email'));
    //     $this->assertEquals(0, $document2->getAttribute('status'));
    //     $this->assertIsInt($document2->getAttribute('status'));
    //     $this->assertEquals(false, $document2->getAttribute('emailVerification'));
    //     $this->assertIsBool($document2->getAttribute('emailVerification'));

    //     $document2 = self::$database->updateDocument(Database::COLLECTION_USERS, $document2->getId(), [
    //         '$id' => $document2->getId(),
    //         '$collection' => Database::COLLECTION_USERS,
    //         '$permissions' => [
    //             'read' => ['user:1234'],
    //             'write' => ['user:1234'],
    //         ],
    //         'email' => 'test5x@appwrite.io',
    //         'emailVerification' => true,
    //         'status' => 1,
    //         'password' => 'secrethashx',
    //         'password-update' => \time(),
    //         'registration' => \time(),
    //         'reset' => true,
    //         'name' => 'Testx',
    //     ]);

    //     $this->assertNotEmpty($document2->getId());
    //     $this->assertIsArray($document2->getPermissions());
    //     $this->assertArrayHasKey('read', $document2->getPermissions());
    //     $this->assertArrayHasKey('write', $document2->getPermissions());
    //     $this->assertEquals('test5x@appwrite.io', $document2->getAttribute('email'));
    //     $this->assertIsString($document2->getAttribute('email'));
    //     $this->assertEquals(1, $document2->getAttribute('status'));
    //     $this->assertIsInt($document2->getAttribute('status'));
    //     $this->assertEquals(true, $document2->getAttribute('emailVerification'));
    //     $this->assertIsBool($document2->getAttribute('emailVerification'));

    //     $document2 = self::$database->getDocument(Database::COLLECTION_USERS, $document2->getId());

    //     $this->assertNotEmpty($document2->getId());
    //     $this->assertIsArray($document2->getPermissions());
    //     $this->assertArrayHasKey('read', $document2->getPermissions());
    //     $this->assertArrayHasKey('write', $document2->getPermissions());
    //     $this->assertEquals('test5x@appwrite.io', $document2->getAttribute('email'));
    //     $this->assertIsString($document2->getAttribute('email'));
    //     $this->assertEquals(1, $document2->getAttribute('status'));
    //     $this->assertIsInt($document2->getAttribute('status'));
    //     $this->assertEquals(true, $document2->getAttribute('emailVerification'));
    //     $this->assertIsBool($document2->getAttribute('emailVerification'));

    //     $types = [
    //         Database::VAR_STRING,
    //         Database::VAR_NUMBER,
    //         Database::VAR_BOOLEAN,
    //         Database::VAR_DOCUMENT,
    //     ];

    //     $rules = [];

    //     foreach($types as $type) {
    //         $rules[] = [
    //             '$collection' => Database::COLLECTION_RULES,
    //             '$permissions' => ['read' => ['*']],
    //             'label' => ucfirst($type),
    //             'key' => $type,
    //             'type' => $type,
    //             'default' => null,
    //             'required' => true,
    //             'array' => false,
    //             'list' => ($type === Database::VAR_DOCUMENT) ? [$collection1->getId()] : [],
    //         ];

    //         $rules[] = [
    //             '$collection' => Database::COLLECTION_RULES,
    //             '$permissions' => ['read' => ['*']],
    //             'label' => ucfirst($type),
    //             'key' => $type.'s',
    //             'type' => $type,
    //             'default' => null,
    //             'required' => true,
    //             'array' => true,
    //             'list' => ($type === Database::VAR_DOCUMENT) ? [$collection1->getId()] : [],
    //         ];
    //     }

    //     $rules[] = [
    //         '$collection' => Database::COLLECTION_RULES,
    //         '$permissions' => ['read' => ['*']],
    //         'label' => 'document2',
    //         'key' => 'document2',
    //         'type' => Database::VAR_DOCUMENT,
    //         'default' => null,
    //         'required' => true,
    //         'array' => false,
    //         'list' => [$collection1->getId()],
    //     ];

    //     $rules[] = [
    //         '$collection' => Database::COLLECTION_RULES,
    //         '$permissions' => ['read' => ['*']],
    //         'label' => 'documents2',
    //         'key' => 'documents2',
    //         'type' => Database::VAR_DOCUMENT,
    //         'default' => null,
    //         'required' => true,
    //         'array' => true,
    //         'list' => [$collection1->getId()],
    //     ];

    //     $collection2 = self::$database->createDocument(Database::COLLECTION_COLLECTIONS, [
    //         '$collection' => Database::COLLECTION_COLLECTIONS,
    //         '$permissions' => ['read' => ['*']],
    //         'name' => 'Create Documents',
    //         'rules' => $rules,
    //     ]);

    //     $this->assertEquals(true, self::$database->createCollection($collection2->getId(), [], []));
        
    //     $document3 = self::$database->createDocument($collection2->getId(), [
    //         '$collection' => $collection2->getId(),
    //         '$permissions' => [
    //             'read' => ['*'],
    //             'write' => ['user:123'],
    //         ],
    //         'text' => 'Hello World',
    //         'texts' => ['Hello World 1', 'Hello World 2'],
    //         // 'document' => $document0,
    //         // 'documents' => [$document0],
    //         'document' => $document0,
    //         'documents' => [$document1, $document0],
    //         'document2' => $document1,
    //         'documents2' => [$document0, $document1],
    //         'integer' => 1,
    //         'integers' => [5, 3, 4],
    //         'float' => 2.22,
    //         'floats' => [1.13, 4.33, 8.9999],
    //         'numeric' => 1,
    //         'numerics' => [1, 5, 7.77],
    //         'boolean' => true,
    //         'booleans' => [true, false, true],
    //         'email' => 'test@appwrite.io',
    //         'emails' => [
    //             'test4@appwrite.io',
    //             'test3@appwrite.io',
    //             'test2@appwrite.io',
    //             'test1@appwrite.io'
    //         ],
    //         'url' => 'http://example.com/welcome',
    //         'urls' => [
    //             'http://example.com/welcome-1',
    //             'http://example.com/welcome-2',
    //             'http://example.com/welcome-3'
    //         ],
    //         'ipv4' => '172.16.254.1',
    //         'ipv4s' => [
    //             '172.16.254.1',
    //             '172.16.254.5'
    //         ],
    //         'ipv6' => '2001:0db8:85a3:0000:0000:8a2e:0370:7334',
    //         'ipv6s' => [
    //             '2001:0db8:85a3:0000:0000:8a2e:0370:7334',
    //             '2001:0db8:85a3:0000:0000:8a2e:0370:7337'
    //         ],
    //         'key' => uniqid(),
    //         'keys' => [uniqid(), uniqid(), uniqid()],
    //     ]);

    //     $document3 = self::$database->getDocument($collection2->getId(), $document3->getId());

    //     $this->assertIsString($document3->getId());
    //     $this->assertIsString($document3->getCollection());
    //     $this->assertEquals([
    //         'read' => ['*'],
    //         'write' => ['user:123'],
    //     ], $document3->getPermissions());
    //     $this->assertEquals('Hello World', $document3->getAttribute('text'));
    //     $this->assertCount(2, $document3->getAttribute('texts'));
        
    //     $this->assertIsString($document3->getAttribute('text'));
    //     $this->assertEquals('Hello World', $document3->getAttribute('text'));
    //     $this->assertEquals(['Hello World 1', 'Hello World 2'], $document3->getAttribute('texts'));
    //     $this->assertCount(2, $document3->getAttribute('texts'));
        
    //     $this->assertInstanceOf(Document::class, $document3->getAttribute('document'));
    //     $this->assertIsString($document3->getAttribute('document')->getId());
    //     $this->assertNotEmpty($document3->getAttribute('document')->getId());
    //     $this->assertIsArray($document3->getAttribute('document')->getPermissions());
    //     $this->assertArrayHasKey('read', $document3->getAttribute('document')->getPermissions());
    //     $this->assertArrayHasKey('write', $document3->getAttribute('document')->getPermissions());
    //     $this->assertEquals('Task #0', $document3->getAttribute('document')->getAttribute('name'));
    //     $this->assertCount(4, $document3->getAttribute('document')->getAttribute('links'));
    //     $this->assertEquals('http://example.com/link-1', $document3->getAttribute('document')->getAttribute('links')[0]);
    //     $this->assertEquals('http://example.com/link-2', $document3->getAttribute('document')->getAttribute('links')[1]);
    //     $this->assertEquals('http://example.com/link-3', $document3->getAttribute('document')->getAttribute('links')[2]);
    //     $this->assertEquals('http://example.com/link-4', $document3->getAttribute('document')->getAttribute('links')[3]);
        
    //     $this->assertInstanceOf(Document::class, $document3->getAttribute('documents')[0]);
    //     $this->assertIsString($document3->getAttribute('documents')[0]->getId());
    //     $this->assertNotEmpty($document3->getAttribute('documents')[0]->getId());
    //     $this->assertIsArray($document3->getAttribute('documents')[0]->getPermissions());
    //     $this->assertArrayHasKey('read', $document3->getAttribute('documents')[0]->getPermissions());
    //     $this->assertArrayHasKey('write', $document3->getAttribute('documents')[0]->getPermissions());
    //     $this->assertEquals('Task #1x', $document3->getAttribute('documents')[0]->getAttribute('name'));
    //     $this->assertCount(4, $document3->getAttribute('documents')[0]->getAttribute('links'));
    //     $this->assertEquals('http://example.com/link-5x', $document3->getAttribute('documents')[0]->getAttribute('links')[0]);
    //     $this->assertEquals('http://example.com/link-6x', $document3->getAttribute('documents')[0]->getAttribute('links')[1]);
    //     $this->assertEquals('http://example.com/link-7x', $document3->getAttribute('documents')[0]->getAttribute('links')[2]);
    //     $this->assertEquals('http://example.com/link-8x', $document3->getAttribute('documents')[0]->getAttribute('links')[3]);
        
    //     $this->assertInstanceOf(Document::class, $document3->getAttribute('documents')[1]);
    //     $this->assertIsString($document3->getAttribute('documents')[1]->getId());
    //     $this->assertNotEmpty($document3->getAttribute('documents')[1]->getId());
    //     $this->assertIsArray($document3->getAttribute('documents')[1]->getPermissions());
    //     $this->assertArrayHasKey('read', $document3->getAttribute('documents')[1]->getPermissions());
    //     $this->assertArrayHasKey('write', $document3->getAttribute('documents')[1]->getPermissions());
    //     $this->assertEquals('Task #0', $document3->getAttribute('documents')[1]->getAttribute('name'));
    //     $this->assertCount(4, $document3->getAttribute('documents')[1]->getAttribute('links'));
    //     $this->assertEquals('http://example.com/link-1', $document3->getAttribute('documents')[1]->getAttribute('links')[0]);
    //     $this->assertEquals('http://example.com/link-2', $document3->getAttribute('documents')[1]->getAttribute('links')[1]);
    //     $this->assertEquals('http://example.com/link-3', $document3->getAttribute('documents')[1]->getAttribute('links')[2]);
    //     $this->assertEquals('http://example.com/link-4', $document3->getAttribute('documents')[1]->getAttribute('links')[3]);

    //     $this->assertInstanceOf(Document::class, $document3->getAttribute('document2'));
    //     $this->assertIsString($document3->getAttribute('document2')->getId());
    //     $this->assertNotEmpty($document3->getAttribute('document2')->getId());
    //     $this->assertIsArray($document3->getAttribute('document2')->getPermissions());
    //     $this->assertArrayHasKey('read', $document3->getAttribute('document2')->getPermissions());
    //     $this->assertArrayHasKey('write', $document3->getAttribute('document2')->getPermissions());
    //     $this->assertEquals('Task #1x', $document3->getAttribute('document2')->getAttribute('name'));
    //     $this->assertCount(4, $document3->getAttribute('document2')->getAttribute('links'));
    //     $this->assertEquals('http://example.com/link-5x', $document3->getAttribute('document2')->getAttribute('links')[0]);
    //     $this->assertEquals('http://example.com/link-6x', $document3->getAttribute('document2')->getAttribute('links')[1]);
    //     $this->assertEquals('http://example.com/link-7x', $document3->getAttribute('document2')->getAttribute('links')[2]);
    //     $this->assertEquals('http://example.com/link-8x', $document3->getAttribute('document2')->getAttribute('links')[3]);

    //     $this->assertInstanceOf(Document::class, $document3->getAttribute('documents2')[0]);
    //     $this->assertIsString($document3->getAttribute('documents2')[0]->getId());
    //     $this->assertNotEmpty($document3->getAttribute('documents2')[0]->getId());
    //     $this->assertIsArray($document3->getAttribute('documents2')[0]->getPermissions());
    //     $this->assertArrayHasKey('read', $document3->getAttribute('documents2')[0]->getPermissions());
    //     $this->assertArrayHasKey('write', $document3->getAttribute('documents2')[0]->getPermissions());
    //     $this->assertEquals('Task #0', $document3->getAttribute('documents2')[0]->getAttribute('name'));
    //     $this->assertCount(4, $document3->getAttribute('documents2')[0]->getAttribute('links'));
    //     $this->assertEquals('http://example.com/link-1', $document3->getAttribute('documents2')[0]->getAttribute('links')[0]);
    //     $this->assertEquals('http://example.com/link-2', $document3->getAttribute('documents2')[0]->getAttribute('links')[1]);
    //     $this->assertEquals('http://example.com/link-3', $document3->getAttribute('documents2')[0]->getAttribute('links')[2]);
    //     $this->assertEquals('http://example.com/link-4', $document3->getAttribute('documents2')[0]->getAttribute('links')[3]);

    //     $this->assertInstanceOf(Document::class, $document3->getAttribute('documents2')[1]);
    //     $this->assertIsString($document3->getAttribute('documents2')[1]->getId());
    //     $this->assertNotEmpty($document3->getAttribute('documents2')[1]->getId());
    //     $this->assertIsArray($document3->getAttribute('documents2')[1]->getPermissions());
    //     $this->assertArrayHasKey('read', $document3->getAttribute('documents2')[1]->getPermissions());
    //     $this->assertArrayHasKey('write', $document3->getAttribute('documents2')[1]->getPermissions());
    //     $this->assertEquals('Task #1x', $document3->getAttribute('documents2')[1]->getAttribute('name'));
    //     $this->assertCount(4, $document3->getAttribute('documents2')[1]->getAttribute('links'));
    //     $this->assertEquals('http://example.com/link-5x', $document3->getAttribute('documents2')[1]->getAttribute('links')[0]);
    //     $this->assertEquals('http://example.com/link-6x', $document3->getAttribute('documents2')[1]->getAttribute('links')[1]);
    //     $this->assertEquals('http://example.com/link-7x', $document3->getAttribute('documents2')[1]->getAttribute('links')[2]);
    //     $this->assertEquals('http://example.com/link-8x', $document3->getAttribute('documents2')[1]->getAttribute('links')[3]);
        
    //     $this->assertIsInt($document3->getAttribute('integer'));
    //     $this->assertEquals(1, $document3->getAttribute('integer'));
    //     $this->assertIsInt($document3->getAttribute('integers')[0]);
    //     $this->assertIsInt($document3->getAttribute('integers')[1]);
    //     $this->assertIsInt($document3->getAttribute('integers')[2]);
    //     $this->assertEquals([5, 3, 4], $document3->getAttribute('integers'));
    //     $this->assertCount(3, $document3->getAttribute('integers'));

    //     $this->assertIsFloat($document3->getAttribute('float'));
    //     $this->assertEquals(2.22, $document3->getAttribute('float'));
    //     $this->assertIsFloat($document3->getAttribute('floats')[0]);
    //     $this->assertIsFloat($document3->getAttribute('floats')[1]);
    //     $this->assertIsFloat($document3->getAttribute('floats')[2]);
    //     $this->assertEquals([1.13, 4.33, 8.9999], $document3->getAttribute('floats'));
    //     $this->assertCount(3, $document3->getAttribute('floats'));

    //     $this->assertIsBool($document3->getAttribute('boolean'));
    //     $this->assertEquals(true, $document3->getAttribute('boolean'));
    //     $this->assertIsBool($document3->getAttribute('booleans')[0]);
    //     $this->assertIsBool($document3->getAttribute('booleans')[1]);
    //     $this->assertIsBool($document3->getAttribute('booleans')[2]);
    //     $this->assertEquals([true, false, true], $document3->getAttribute('booleans'));
    //     $this->assertCount(3, $document3->getAttribute('booleans'));

    //     $this->assertIsString($document3->getAttribute('email'));
    //     $this->assertEquals('test@appwrite.io', $document3->getAttribute('email'));
    //     $this->assertIsString($document3->getAttribute('emails')[0]);
    //     $this->assertIsString($document3->getAttribute('emails')[1]);
    //     $this->assertIsString($document3->getAttribute('emails')[2]);
    //     $this->assertIsString($document3->getAttribute('emails')[3]);
    //     $this->assertEquals([
    //         'test4@appwrite.io',
    //         'test3@appwrite.io',
    //         'test2@appwrite.io',
    //         'test1@appwrite.io'
    //     ], $document3->getAttribute('emails'));
    //     $this->assertCount(4, $document3->getAttribute('emails'));

    //     $this->assertIsString($document3->getAttribute('url'));
    //     $this->assertEquals('http://example.com/welcome', $document3->getAttribute('url'));
    //     $this->assertIsString($document3->getAttribute('urls')[0]);
    //     $this->assertIsString($document3->getAttribute('urls')[1]);
    //     $this->assertIsString($document3->getAttribute('urls')[2]);
    //     $this->assertEquals([
    //         'http://example.com/welcome-1',
    //         'http://example.com/welcome-2',
    //         'http://example.com/welcome-3'
    //     ], $document3->getAttribute('urls'));
    //     $this->assertCount(3, $document3->getAttribute('urls'));

    //     $this->assertIsString($document3->getAttribute('ipv4'));
    //     $this->assertEquals('172.16.254.1', $document3->getAttribute('ipv4'));
    //     $this->assertIsString($document3->getAttribute('ipv4s')[0]);
    //     $this->assertIsString($document3->getAttribute('ipv4s')[1]);
    //     $this->assertEquals([
    //         '172.16.254.1',
    //         '172.16.254.5'
    //     ], $document3->getAttribute('ipv4s'));
    //     $this->assertCount(2, $document3->getAttribute('ipv4s'));

    //     $this->assertIsString($document3->getAttribute('ipv6'));
    //     $this->assertEquals('2001:0db8:85a3:0000:0000:8a2e:0370:7334', $document3->getAttribute('ipv6'));
    //     $this->assertIsString($document3->getAttribute('ipv6s')[0]);
    //     $this->assertIsString($document3->getAttribute('ipv6s')[1]);
    //     $this->assertEquals([
    //         '2001:0db8:85a3:0000:0000:8a2e:0370:7334',
    //         '2001:0db8:85a3:0000:0000:8a2e:0370:7337'
    //     ], $document3->getAttribute('ipv6s'));
    //     $this->assertCount(2, $document3->getAttribute('ipv6s'));

    //     $this->assertEquals('2001:0db8:85a3:0000:0000:8a2e:0370:7334', $document3->getAttribute('ipv6'));
    //     $this->assertEquals([
    //         '2001:0db8:85a3:0000:0000:8a2e:0370:7334',
    //         '2001:0db8:85a3:0000:0000:8a2e:0370:7337'
    //     ], $document3->getAttribute('ipv6s'));
    //     $this->assertCount(2, $document3->getAttribute('ipv6s'));

    //     $this->assertIsString($document3->getAttribute('key'));
    //     $this->assertCount(3, $document3->getAttribute('keys'));

    //     // Update

    //     $document3 = self::$database->updateDocument($collection2->getId(), $document3->getId(), [
    //         '$id' => $document3->getId(),
    //         '$collection' => $collection2->getId(),
    //         '$permissions' => [
    //             'read' => ['user:1234'],
    //             'write' => ['user:1234'],
    //         ],
    //         'text' => 'Hello Worldx',
    //         'texts' => ['Hello World 1x', 'Hello World 2x'],
    //         'document' => $document0,
    //         'documents' => [$document1, $document0],
    //         'document2' => $document1,
    //         'documents2' => [$document0, $document1],
    //         'integer' => 2,
    //         'integers' => [6, 4, 5],
    //         'float' => 3.22,
    //         'floats' => [2.13, 5.33, 9.9999],
    //         'numeric' => 2,
    //         'numerics' => [2, 6, 8.77],
    //         'boolean' => false,
    //         'booleans' => [false, true, false],
    //         'email' => 'testx@appwrite.io',
    //         'emails' => [
    //             'test4x@appwrite.io',
    //             'test3x@appwrite.io',
    //             'test2x@appwrite.io',
    //             'test1x@appwrite.io'
    //         ],
    //         'url' => 'http://example.com/welcomex',
    //         'urls' => [
    //             'http://example.com/welcome-1x',
    //             'http://example.com/welcome-2x',
    //             'http://example.com/welcome-3x'
    //         ],
    //         'ipv4' => '172.16.254.2',
    //         'ipv4s' => [
    //             '172.16.254.2',
    //             '172.16.254.6'
    //         ],
    //         'ipv6' => '2001:0db8:85a3:0000:0000:8a2e:0370:7335',
    //         'ipv6s' => [
    //             '2001:0db8:85a3:0000:0000:8a2e:0370:7335',
    //             '2001:0db8:85a3:0000:0000:8a2e:0370:7338'
    //         ],
    //         'key' => uniqid().'x',
    //         'keys' => [uniqid().'x', uniqid().'x', uniqid().'x'],
    //     ]);

    //     $document3 = self::$database->getDocument($collection2->getId(), $document3->getId());

    //     $this->assertIsString($document3->getId());
    //     $this->assertIsString($document3->getCollection());
    //     $this->assertEquals([
    //         'read' => ['user:1234'],
    //         'write' => ['user:1234'],
    //     ], $document3->getPermissions());
    //     $this->assertEquals('Hello Worldx', $document3->getAttribute('text'));
    //     $this->assertCount(2, $document3->getAttribute('texts'));
        
    //     $this->assertIsString($document3->getAttribute('text'));
    //     $this->assertEquals('Hello Worldx', $document3->getAttribute('text'));
    //     $this->assertEquals(['Hello World 1x', 'Hello World 2x'], $document3->getAttribute('texts'));
    //     $this->assertCount(2, $document3->getAttribute('texts'));
        
    //     $this->assertInstanceOf(Document::class, $document3->getAttribute('document'));
    //     $this->assertIsString($document3->getAttribute('document')->getId());
    //     $this->assertNotEmpty($document3->getAttribute('document')->getId());
    //     $this->assertIsArray($document3->getAttribute('document')->getPermissions());
    //     $this->assertArrayHasKey('read', $document3->getAttribute('document')->getPermissions());
    //     $this->assertArrayHasKey('write', $document3->getAttribute('document')->getPermissions());
    //     $this->assertEquals('Task #0', $document3->getAttribute('document')->getAttribute('name'));
    //     $this->assertCount(4, $document3->getAttribute('document')->getAttribute('links'));
    //     $this->assertEquals('http://example.com/link-1', $document3->getAttribute('document')->getAttribute('links')[0]);
    //     $this->assertEquals('http://example.com/link-2', $document3->getAttribute('document')->getAttribute('links')[1]);
    //     $this->assertEquals('http://example.com/link-3', $document3->getAttribute('document')->getAttribute('links')[2]);
    //     $this->assertEquals('http://example.com/link-4', $document3->getAttribute('document')->getAttribute('links')[3]);
        
    //     $this->assertInstanceOf(Document::class, $document3->getAttribute('documents')[0]);
    //     $this->assertIsString($document3->getAttribute('documents')[0]->getId());
    //     $this->assertNotEmpty($document3->getAttribute('documents')[0]->getId());
    //     $this->assertIsArray($document3->getAttribute('documents')[0]->getPermissions());
    //     $this->assertArrayHasKey('read', $document3->getAttribute('documents')[0]->getPermissions());
    //     $this->assertArrayHasKey('write', $document3->getAttribute('documents')[0]->getPermissions());
    //     $this->assertEquals('Task #1x', $document3->getAttribute('documents')[0]->getAttribute('name'));
    //     $this->assertCount(4, $document3->getAttribute('documents')[0]->getAttribute('links'));
    //     $this->assertEquals('http://example.com/link-5x', $document3->getAttribute('documents')[0]->getAttribute('links')[0]);
    //     $this->assertEquals('http://example.com/link-6x', $document3->getAttribute('documents')[0]->getAttribute('links')[1]);
    //     $this->assertEquals('http://example.com/link-7x', $document3->getAttribute('documents')[0]->getAttribute('links')[2]);
    //     $this->assertEquals('http://example.com/link-8x', $document3->getAttribute('documents')[0]->getAttribute('links')[3]);
        
    //     $this->assertInstanceOf(Document::class, $document3->getAttribute('documents')[1]);
    //     $this->assertIsString($document3->getAttribute('documents')[1]->getId());
    //     $this->assertNotEmpty($document3->getAttribute('documents')[1]->getId());
    //     $this->assertIsArray($document3->getAttribute('documents')[1]->getPermissions());
    //     $this->assertArrayHasKey('read', $document3->getAttribute('documents')[1]->getPermissions());
    //     $this->assertArrayHasKey('write', $document3->getAttribute('documents')[1]->getPermissions());
    //     $this->assertEquals('Task #0', $document3->getAttribute('documents')[1]->getAttribute('name'));
    //     $this->assertCount(4, $document3->getAttribute('documents')[1]->getAttribute('links'));
    //     $this->assertEquals('http://example.com/link-1', $document3->getAttribute('documents')[1]->getAttribute('links')[0]);
    //     $this->assertEquals('http://example.com/link-2', $document3->getAttribute('documents')[1]->getAttribute('links')[1]);
    //     $this->assertEquals('http://example.com/link-3', $document3->getAttribute('documents')[1]->getAttribute('links')[2]);
    //     $this->assertEquals('http://example.com/link-4', $document3->getAttribute('documents')[1]->getAttribute('links')[3]);

    //     $this->assertInstanceOf(Document::class, $document3->getAttribute('document2'));
    //     $this->assertIsString($document3->getAttribute('document2')->getId());
    //     $this->assertNotEmpty($document3->getAttribute('document2')->getId());
    //     $this->assertIsArray($document3->getAttribute('document2')->getPermissions());
    //     $this->assertArrayHasKey('read', $document3->getAttribute('document2')->getPermissions());
    //     $this->assertArrayHasKey('write', $document3->getAttribute('document2')->getPermissions());
    //     $this->assertEquals('Task #1x', $document3->getAttribute('document2')->getAttribute('name'));
    //     $this->assertCount(4, $document3->getAttribute('document2')->getAttribute('links'));
    //     $this->assertEquals('http://example.com/link-5x', $document3->getAttribute('document2')->getAttribute('links')[0]);
    //     $this->assertEquals('http://example.com/link-6x', $document3->getAttribute('document2')->getAttribute('links')[1]);
    //     $this->assertEquals('http://example.com/link-7x', $document3->getAttribute('document2')->getAttribute('links')[2]);
    //     $this->assertEquals('http://example.com/link-8x', $document3->getAttribute('document2')->getAttribute('links')[3]);

    //     $this->assertInstanceOf(Document::class, $document3->getAttribute('documents2')[0]);
    //     $this->assertIsString($document3->getAttribute('documents2')[0]->getId());
    //     $this->assertNotEmpty($document3->getAttribute('documents2')[0]->getId());
    //     $this->assertIsArray($document3->getAttribute('documents2')[0]->getPermissions());
    //     $this->assertArrayHasKey('read', $document3->getAttribute('documents2')[0]->getPermissions());
    //     $this->assertArrayHasKey('write', $document3->getAttribute('documents2')[0]->getPermissions());
    //     $this->assertEquals('Task #0', $document3->getAttribute('documents2')[0]->getAttribute('name'));
    //     $this->assertCount(4, $document3->getAttribute('documents2')[0]->getAttribute('links'));
    //     $this->assertEquals('http://example.com/link-1', $document3->getAttribute('documents2')[0]->getAttribute('links')[0]);
    //     $this->assertEquals('http://example.com/link-2', $document3->getAttribute('documents2')[0]->getAttribute('links')[1]);
    //     $this->assertEquals('http://example.com/link-3', $document3->getAttribute('documents2')[0]->getAttribute('links')[2]);
    //     $this->assertEquals('http://example.com/link-4', $document3->getAttribute('documents2')[0]->getAttribute('links')[3]);

    //     $this->assertInstanceOf(Document::class, $document3->getAttribute('documents2')[1]);
    //     $this->assertIsString($document3->getAttribute('documents2')[1]->getId());
    //     $this->assertNotEmpty($document3->getAttribute('documents2')[1]->getId());
    //     $this->assertIsArray($document3->getAttribute('documents2')[1]->getPermissions());
    //     $this->assertArrayHasKey('read', $document3->getAttribute('documents2')[1]->getPermissions());
    //     $this->assertArrayHasKey('write', $document3->getAttribute('documents2')[1]->getPermissions());
    //     $this->assertEquals('Task #1x', $document3->getAttribute('documents2')[1]->getAttribute('name'));
    //     $this->assertCount(4, $document3->getAttribute('documents2')[1]->getAttribute('links'));
    //     $this->assertEquals('http://example.com/link-5x', $document3->getAttribute('documents2')[1]->getAttribute('links')[0]);
    //     $this->assertEquals('http://example.com/link-6x', $document3->getAttribute('documents2')[1]->getAttribute('links')[1]);
    //     $this->assertEquals('http://example.com/link-7x', $document3->getAttribute('documents2')[1]->getAttribute('links')[2]);
    //     $this->assertEquals('http://example.com/link-8x', $document3->getAttribute('documents2')[1]->getAttribute('links')[3]);
        
    //     $this->assertIsInt($document3->getAttribute('integer'));
    //     $this->assertEquals(2, $document3->getAttribute('integer'));
    //     $this->assertIsInt($document3->getAttribute('integers')[0]);
    //     $this->assertIsInt($document3->getAttribute('integers')[1]);
    //     $this->assertIsInt($document3->getAttribute('integers')[2]);
    //     $this->assertEquals([6, 4, 5], $document3->getAttribute('integers'));
    //     $this->assertCount(3, $document3->getAttribute('integers'));

    //     $this->assertIsFloat($document3->getAttribute('float'));
    //     $this->assertEquals(3.22, $document3->getAttribute('float'));
    //     $this->assertIsFloat($document3->getAttribute('floats')[0]);
    //     $this->assertIsFloat($document3->getAttribute('floats')[1]);
    //     $this->assertIsFloat($document3->getAttribute('floats')[2]);
    //     $this->assertEquals([2.13, 5.33, 9.9999], $document3->getAttribute('floats'));
    //     $this->assertCount(3, $document3->getAttribute('floats'));

    //     $this->assertIsBool($document3->getAttribute('boolean'));
    //     $this->assertEquals(false, $document3->getAttribute('boolean'));
    //     $this->assertIsBool($document3->getAttribute('booleans')[0]);
    //     $this->assertIsBool($document3->getAttribute('booleans')[1]);
    //     $this->assertIsBool($document3->getAttribute('booleans')[2]);
    //     $this->assertEquals([false, true, false], $document3->getAttribute('booleans'));
    //     $this->assertCount(3, $document3->getAttribute('booleans'));

    //     $this->assertIsString($document3->getAttribute('email'));
    //     $this->assertEquals('testx@appwrite.io', $document3->getAttribute('email'));
    //     $this->assertIsString($document3->getAttribute('emails')[0]);
    //     $this->assertIsString($document3->getAttribute('emails')[1]);
    //     $this->assertIsString($document3->getAttribute('emails')[2]);
    //     $this->assertIsString($document3->getAttribute('emails')[3]);
    //     $this->assertEquals([
    //         'test4x@appwrite.io',
    //         'test3x@appwrite.io',
    //         'test2x@appwrite.io',
    //         'test1x@appwrite.io'
    //     ], $document3->getAttribute('emails'));
    //     $this->assertCount(4, $document3->getAttribute('emails'));

    //     $this->assertIsString($document3->getAttribute('url'));
    //     $this->assertEquals('http://example.com/welcomex', $document3->getAttribute('url'));
    //     $this->assertIsString($document3->getAttribute('urls')[0]);
    //     $this->assertIsString($document3->getAttribute('urls')[1]);
    //     $this->assertIsString($document3->getAttribute('urls')[2]);
    //     $this->assertEquals([
    //         'http://example.com/welcome-1x',
    //         'http://example.com/welcome-2x',
    //         'http://example.com/welcome-3x'
    //     ], $document3->getAttribute('urls'));
    //     $this->assertCount(3, $document3->getAttribute('urls'));

    //     $this->assertIsString($document3->getAttribute('ipv4'));
    //     $this->assertEquals('172.16.254.2', $document3->getAttribute('ipv4'));
    //     $this->assertIsString($document3->getAttribute('ipv4s')[0]);
    //     $this->assertIsString($document3->getAttribute('ipv4s')[1]);
    //     $this->assertEquals([
    //         '172.16.254.2',
    //         '172.16.254.6'
    //     ], $document3->getAttribute('ipv4s'));
    //     $this->assertCount(2, $document3->getAttribute('ipv4s'));

    //     $this->assertIsString($document3->getAttribute('ipv6'));
    //     $this->assertEquals('2001:0db8:85a3:0000:0000:8a2e:0370:7335', $document3->getAttribute('ipv6'));
    //     $this->assertIsString($document3->getAttribute('ipv6s')[0]);
    //     $this->assertIsString($document3->getAttribute('ipv6s')[1]);
    //     $this->assertEquals([
    //         '2001:0db8:85a3:0000:0000:8a2e:0370:7335',
    //         '2001:0db8:85a3:0000:0000:8a2e:0370:7338'
    //     ], $document3->getAttribute('ipv6s'));
    //     $this->assertCount(2, $document3->getAttribute('ipv6s'));

    //     $this->assertEquals('2001:0db8:85a3:0000:0000:8a2e:0370:7335', $document3->getAttribute('ipv6'));
    //     $this->assertEquals([
    //         '2001:0db8:85a3:0000:0000:8a2e:0370:7335',
    //         '2001:0db8:85a3:0000:0000:8a2e:0370:7338'
    //     ], $document3->getAttribute('ipv6s'));
    //     $this->assertCount(2, $document3->getAttribute('ipv6s'));

    //     $this->assertIsString($document3->getAttribute('key'));
    //     $this->assertCount(3, $document3->getAttribute('keys'));
    // }

    // public function testDeleteDocument()
    // {
    //     $collection1 = self::$database->createDocument(Database::COLLECTION_COLLECTIONS, [
    //         '$collection' => Database::COLLECTION_COLLECTIONS,
    //         '$permissions' => ['read' => ['*']],
    //         'name' => 'Create Documents',
    //         'rules' => [
    //             [
    //                 '$collection' => Database::COLLECTION_RULES,
    //                 '$permissions' => ['read' => ['*']],
    //                 'label' => 'Name',
    //                 'key' => 'name',
    //                 'type' => Database::VAR_STRING,
    //                 'default' => '',
    //                 'required' => true,
    //                 'array' => false,
    //             ],
    //             [
    //                 '$collection' => Database::COLLECTION_RULES,
    //                 '$permissions' => ['read' => ['*']],
    //                 'label' => 'Links',
    //                 'key' => 'links',
    //                 'type' => Database::VAR_STRING,
    //                 'default' => '',
    //                 'required' => true,
    //                 'array' => true,
    //             ],
    //         ]
    //     ]);

    //     $this->assertEquals(true, self::$database->createCollection($collection1->getId(), [], []));
        
    //     $document1 = self::$database->createDocument($collection1->getId(), [
    //         '$collection' => $collection1->getId(),
    //         '$permissions' => [
    //             'read' => ['*'],
    //             'write' => ['user:123'],
    //         ],
    //         'name' => 'Task #1️⃣',
    //         'links' => [
    //             'http://example.com/link-5',
    //             'http://example.com/link-6',
    //             'http://example.com/link-7',
    //             'http://example.com/link-8',
    //         ],
    //     ]);

    //     $this->assertNotEmpty($document1->getId());
    //     $this->assertIsArray($document1->getPermissions());
    //     $this->assertArrayHasKey('read', $document1->getPermissions());
    //     $this->assertArrayHasKey('write', $document1->getPermissions());
    //     $this->assertEquals('Task #1️⃣', $document1->getAttribute('name'));
    //     $this->assertCount(4, $document1->getAttribute('links'));
    //     $this->assertEquals('http://example.com/link-5', $document1->getAttribute('links')[0]);
    //     $this->assertEquals('http://example.com/link-6', $document1->getAttribute('links')[1]);
    //     $this->assertEquals('http://example.com/link-7', $document1->getAttribute('links')[2]);
    //     $this->assertEquals('http://example.com/link-8', $document1->getAttribute('links')[3]);
        
    //     $document1 = self::$database->getDocument($collection1->getId(), $document1->getId());

    //     $this->assertNotEmpty($document1->getId());
    //     $this->assertIsArray($document1->getPermissions());
    //     $this->assertArrayHasKey('read', $document1->getPermissions());
    //     $this->assertArrayHasKey('write', $document1->getPermissions());
    //     $this->assertEquals('Task #1️⃣', $document1->getAttribute('name'));
    //     $this->assertCount(4, $document1->getAttribute('links'));
    //     $this->assertEquals('http://example.com/link-5', $document1->getAttribute('links')[0]);
    //     $this->assertEquals('http://example.com/link-6', $document1->getAttribute('links')[1]);
    //     $this->assertEquals('http://example.com/link-7', $document1->getAttribute('links')[2]);
    //     $this->assertEquals('http://example.com/link-8', $document1->getAttribute('links')[3]);

    //     self::$database->deleteDocument($collection1->getId(), $document1->getId());

    //     $document1 = self::$database->getDocument($collection1->getId(), $document1->getId());

    //     $this->assertTrue($document1->isEmpty());
    //     $this->assertEmpty($document1->getId());
    //     $this->assertEmpty($document1->getCollection());
    //     $this->assertIsArray($document1->getPermissions());
    //     $this->assertEmpty($document1->getPermissions());
    // }

    // public function testFind()
    // {
    //     $data = include __DIR__.'/../../resources/database/movies.php';

    //     $collections = $data['collections'];
    //     $movies = $data['movies'];

    //     foreach ($collections as $key => &$collection) {
    //         $collection = self::$database->createDocument(Database::COLLECTION_COLLECTIONS, $collection);
    //         self::$database->createCollection($collection->getId(), [], []);
    //     }

    //     foreach ($movies as $key => &$movie) {
    //         $movie['$collection'] = $collection->getId();
    //         $movie['$permissions'] = [];
    //         $movie = self::$database->createDocument($collection->getId(), $movie);            
    //     }

    //     self::$database->find($collection->getId(), [
    //         'limit' => 5,
    //         'filters' => [
    //             'name=Hello World',
    //             'releaseYear=1999',
    //             'langauges=English',
    //         ],
    //     ]);
    //     $this->assertEquals('1', '1');
    // }

    public function testFindFirst()
    {
        $this->assertEquals('1', '1');
    }

    public function testFindLast()
    {
        $this->assertEquals('1', '1');
    }

    public function countTest()
    {
        $this->assertEquals('1', '1');
    }

    public function addFilterTest()
    {
        $this->assertEquals('1', '1');
    }

    public function encodeTest()
    {
        $this->assertEquals('1', '1');
    }

    public function decodeTest()
    {
        $this->assertEquals('1', '1');
    }
}