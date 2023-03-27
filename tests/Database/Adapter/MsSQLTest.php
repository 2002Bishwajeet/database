<?php

namespace Utopia\Tests\Adapter;

use PDO;
use Redis;
use Utopia\Cache\Cache;
use Utopia\Database\Database;
use Utopia\Database\Adapter\MySQL;
use Utopia\Cache\Adapter\Redis as RedisAdapter;
use Utopia\Database\Adapter\MsSql;
use Utopia\Tests\Base;

class MsSQLTest extends Base
{
    public static ?Database $database = null;

    // TODO@kodumbeats hacky way to identify adapters for tests
    // Remove once all methods are implemented
    /**
     * Return name of adapter
     *
     * @return string
     */
    public static function getAdapterName(): string
    {
        return "mssql";
    }

    /**
     *
     * @return int
     */
    public static function getUsedIndexes(): int
    {
        return MsSql::getCountOfDefaultIndexes();
    }

    /**
     * @return Database
     */
    public static function getDatabase(): Database
    {
        if (!is_null(self::$database)) {
            return self::$database;
        }

        $dbHost = 'mssql';
        $dbPort = '3310';
        $dbUser = 'admin';
        $dbPass = 'admin';

        $pdo = new PDO("sqlsrv:Server={$dbHost};port={$dbPort};charset=utf8mb4", $dbUser, $dbPass, MsSql::getPDOAttributes());

        $redis = new Redis();
        $redis->connect('redis', 6379);
        $redis->flushAll();

        $cache = new Cache(new RedisAdapter($redis));

        $database = new Database(new MsSql($pdo), $cache);
        $database->setDefaultDatabase('utopiaTests');
        $database->setNamespace('myapp_' . uniqid());

        return self::$database = $database;
    }
}
