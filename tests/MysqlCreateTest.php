<?php

namespace Scheezy\Tests;

class MysqlCreateTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->pdo = new \PDO("mysql:host=localhost;dbname=scheezy", 'scheezy', '');
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec('DROP TABLE IF EXISTS `store`');
    }

    public function testTableName()
    {
        $schema = new \Scheezy\Schema(dirname(__FILE__) . '/schemas/store.yaml', $this->pdo);
        $this->assertEquals('store', $schema->getTableName());
    }

    public function testCreate()
    {
        $schema = new \Scheezy\Schema(dirname(__FILE__) . '/schemas/store.yaml', $this->pdo);
        $sql = $schema->toString();

        $expected = <<<END
CREATE TABLE `store` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(80) NOT NULL,
`active` tinyint(1) NOT NULL,
`user_count` int(11) NOT NULL,
`website` varchar(255) NOT NULL,
`phone` varchar(255) NOT NULL,
PRIMARY KEY (`id`)
)
END;

        $this->assertEquals($expected, $sql);

    }

    public function testCreateJoins()
    {
        $schema = new \Scheezy\Schema(dirname(__FILE__) . '/schemas/store_user_join.yaml', $this->pdo);
        $sql = $schema->toString();

        $expected = <<<END
CREATE TABLE `store_user_join` (
`store_id` int(11) NOT NULL,
`user_id` int(11) NOT NULL,
INDEX (`store_id`),
INDEX (`user_id`)
)
END;

        $this->assertEquals($expected, $sql);

    }
}
