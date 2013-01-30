<?php

namespace Scheezy\Tests;

class MysqlChangeTest extends ScheezyTestSuite
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
        $this->pdo = $this->getMysqlPdo();
        $this->pdo->exec('DROP TABLE IF EXISTS `store`');
        $this->pdo->exec($sql);

    }

    public function testAddColumns()
    {
        $schema = new \Scheezy\Schema($this->pdo);
        $schema->loadFile(dirname(__FILE__) . '/schemas/store.yaml');
        $sql = $schema->toString();

        $expected = <<<END
ALTER TABLE `store`
ADD COLUMN `name` varchar(80) NOT NULL,
ADD COLUMN `active` tinyint(1) NOT NULL,
ADD COLUMN `user_count` int(11) NOT NULL,
ADD COLUMN `website` varchar(255) NOT NULL
END;

        $this->assertEquals($expected, $sql);
        $schema->synchronize();

    }

    public function testDropColumns()
    {
        $yaml = <<<END
table: store
columns:
    id:
END;

        $schema = new \Scheezy\Schema($this->pdo);
        $schema->loadString($yaml);
        $sql = $schema->toString();

        $expected = <<<END
ALTER TABLE `store`
DROP COLUMN `phone`
END;

        $this->assertEquals($expected, $sql);
        $schema->synchronize();

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

        $schema = new \Scheezy\Schema($this->pdo);
        $schema->loadFile(dirname(__FILE__) . '/schemas/store.yaml');
        $sql = $schema->toString();

        $expected = <<<END
ALTER TABLE `store`
CHANGE `name` `name` varchar(80) NOT NULL,
CHANGE `active` `active` tinyint(1) NOT NULL
END;

        $this->assertEquals($expected, $sql);
        $schema->synchronize();

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

        $schema = new \Scheezy\Schema($this->pdo);
        $schema->loadString($yaml);
        $sql = $schema->toString();

        $expected = <<<END
ALTER TABLE `store`
ADD COLUMN `email` varchar(255) NOT NULL,
ADD COLUMN `type` varchar(255) NOT NULL;
UNIQUE (`email`) ON `store`;
INDEX (`type`) ON `store`
END;

        $this->assertEquals($expected, $sql);
        $schema->synchronize();

    }
}
