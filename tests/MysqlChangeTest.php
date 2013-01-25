<?php

namespace Scheezy\Tests;

class MysqlChangeTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $sql = <<<END
CREATE TABLE `store` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`phone` varchar(255) NOT NULL,
PRIMARY KEY (`id`)
)
END;
        $this->pdo = new \PDO("mysql:host=localhost;dbname=scheezy", 'scheezy', '');
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec('DROP TABLE IF EXISTS `store`');
        $this->pdo->exec($sql);

    }

    public function testAddColumns()
    {
        $schema = new \Scheezy\Schema(dirname(__FILE__) . '/schemas/store.yaml', $this->pdo);
        $sql = $schema->toString();

        $expected = <<<END
ALTER TABLE `store` (
ADD COLUMN `name` varchar(80) NOT NULL,
ADD COLUMN `active` tinyint(1) NOT NULL,
ADD COLUMN `user_count` int(11) NOT NULL,
ADD COLUMN `website` varchar(255) NOT NULL
)
END;

        $this->assertEquals($expected, $sql);

    }

    public function testDropColumns()
    {
        $yaml = <<<END
table: store
columns:
    id:
END;


        $schema = new \Scheezy\Schema($yaml, $this->pdo);
        $sql = $schema->toString();

        $expected = <<<END
ALTER TABLE `store` (
DROP COLUMN `phone`
)
END;

        $this->assertEquals($expected, $sql);

    }

    public function testAlterColumns()
    {
        $sql = <<<END
CREATE TABLE `store` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(90) NOT NULL,
`active` int(11) NOT NULL,
`user_count` int(11) NOT NULL,
`website` varchar(255) NOT NULL,
`phone` varchar(255) NOT NULL,
PRIMARY KEY (`id`)
)
END;

        $this->pdo->exec('DROP TABLE IF EXISTS `store`');
        $this->pdo->exec($sql);

        $schema = new \Scheezy\Schema(dirname(__FILE__) . '/schemas/store.yaml', $this->pdo);
        $sql = $schema->toString();

        $expected = <<<END
ALTER TABLE `store` (
CHANGE `name` varchar(80) NOT NULL,
CHANGE `active` tinyint(1) NOT NULL
)
END;

        $this->assertEquals($expected, $sql);

    }

    public function testAddIndex()
    {
        $yaml = <<<END
table: store
columns:
    id:
    phone:
    email:
        index: unique
    type:
        index: true
END;


        $schema = new \Scheezy\Schema($yaml, $this->pdo);
        $sql = $schema->toString();

        $expected = <<<END
ALTER TABLE `store` (
ADD COLUMN `email` varchar(255) NOT NULL,
ADD COLUMN `type` varchar(255) NOT NULL,
UNIQUE (`email`),
INDEX (`type`)
)
END;

        $this->assertEquals($expected, $sql);

    }
}
