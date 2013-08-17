<?php

namespace Scheezy\Tests;

class MysqlCreateTest extends ScheezyTestSuite
{

    public function setUp()
    {
        $this->pdo = $this->getMysqlPdo();
        $this->pdo->exec('DROP TABLE IF EXISTS `store`');
        $this->pdo->exec('DROP TABLE IF EXISTS `store_user_join`');
    }

    public function testTableName()
    {
        $schema = new \Scheezy\Schema($this->pdo);
        $db = $schema->loadFile(dirname(__FILE__) . '/schemas/store.yaml');
        $this->assertEquals('store', $db->getTableName());
    }

    public function testCreate()
    {
        $schema = new \Scheezy\Schema($this->pdo);
        $schema->loadFile(dirname(__FILE__) . '/schemas/store.yaml');
        $sql = $schema->__toString();

        $expected = <<<END
CREATE TABLE `store` (
`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
`name` varchar(80) NOT NULL,
`active` tinyint(1) NOT NULL,
`user_count` int(11) NOT NULL,
`website` varchar(255) NOT NULL,
`phone` varchar(255) NOT NULL
)
END;

        $this->assertEquals($expected, $sql);

    }

    public function testCreateJoins()
    {
        $schema = new \Scheezy\Schema($this->pdo);
        $schema->loadFile(dirname(__FILE__) . '/schemas/store_user_join.yaml');
        $sql = $schema->__toString();

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
