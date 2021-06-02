<?php
namespace tests;

use Pdo;

use Marmot\Core;

/**
 * @SuppressWarnings(PHPMD)
 */
trait DbTrait
{
    protected function getConnection()
    {
        $pdo = new PDO(
            $GLOBALS['DB_DSN'],
            $GLOBALS['DB_USER'],
            $GLOBALS['DB_PASSWD'],
            array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
        );
        return $this->createDefaultDBConnection($pdo, $GLOBALS['DB_DBNAME']);
    }

    protected function clear(string $tableName)
    {
        Core::$cacheDriver->flushAll();
        $conn = $this->getConnection();
        $pdo = $conn->getConnection();
        $pdo->exec('TRUNCATE TABLE '.$tableName.';');
    }
}