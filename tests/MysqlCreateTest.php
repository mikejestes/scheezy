<?php

namespace Scheezy\Tests;

class MysqlCreateTest extends \PHPUnit_Framework_TestCase
{

    public function testTableName()
    {
        $schema = new \Scheezy\Schema(dirname(__FILE__) . '/schemas/store.yaml');
        $this->assertEquals('store', $schema->getTableName());
    }

    public function testCreate()
    {
        $schema = new \Scheezy\Schema(dirname(__FILE__) . '/schemas/store.yaml');
        $sql = $schema->toString('mysql');

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
}
