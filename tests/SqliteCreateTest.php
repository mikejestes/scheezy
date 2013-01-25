<?php

namespace Scheezy\Tests;

class SqliteCreateTest extends \PHPUnit_Framework_TestCase
{

    public function testCreate()
    {
        $schema = new \Scheezy\Schema(dirname(__FILE__) . '/schemas/store.yaml');
        $sql = $schema->toString('sqlite');

        $expected = <<<END
CREATE TABLE `store` (
`id` INTEGER PRIMARY KEY AUTOINCREMENT,
`name` varchar(80) NOT NULL,
`active` tinyint(1) NOT NULL,
`user_count` INTEGER NOT NULL,
`website` varchar(255) NOT NULL,
`phone` varchar(255) NOT NULL
)
END;

        $this->assertEquals($expected, $sql);
    }

    public function testExecute()
    {
        $pdo = new \PDO('sqlite::memory:');
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $schema = new \Scheezy\Schema(dirname(__FILE__) . '/schemas/store.yaml');
        $sql = $schema->execute($pdo);

        $stmt = $pdo->query("select name from sqlite_master where type = 'table' and name = 'store'");
        $this->assertEquals(array('name' => 'store'), $stmt->fetch(\PDO::FETCH_ASSOC));
    }
}
