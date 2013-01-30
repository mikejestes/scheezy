<?php

namespace Scheezy\Tests;

class SqliteCreateTest extends ScheezyTestSuite
{

    public function setUp()
    {
        $this->pdo = new \PDO('sqlite::memory:');
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec('DROP TABLE IF EXISTS `store`');
        $this->pdo->exec('DROP TABLE IF EXISTS `store_user_join`');
    }

    public function testCreate()
    {
        $schema = new \Scheezy\Schema($this->pdo);
        $schema->loadFile(dirname(__FILE__) . '/schemas/store.yaml');
        $sql = $schema->toString();

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
        $schema = new \Scheezy\Schema($this->pdo);
        $schema->loadFile(dirname(__FILE__) . '/schemas/store.yaml');
        $schema->synchronize();

        $stmt = $this->pdo->query("select name from sqlite_master where type = 'table' and name = 'store'");
        $this->assertEquals(array('name' => 'store'), $stmt->fetch(\PDO::FETCH_ASSOC));
    }

    public function testCreateJoins()
    {
        $schema = new \Scheezy\Schema($this->pdo);
        $schema->loadFile(dirname(__FILE__) . '/schemas/store_user_join.yaml');
        $sql = $schema->toString();

        $expected = <<<END
CREATE TABLE `store_user_join` (
`store_id` INTEGER NOT NULL,
`user_id` INTEGER NOT NULL
);
CREATE INDEX `store_id` ON `store_user_join` (`store_id`);
CREATE INDEX `user_id` ON `store_user_join` (`user_id`)
END;

        $this->assertEquals($expected, $sql);
        $schema->synchronize();

    }
}
