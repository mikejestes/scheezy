<?php

namespace Scheezy\Tests;

class SqliteChangeTest extends ScheezyTestSuite
{

    public function setUp()
    {
        $sql = <<<END
CREATE TABLE `store` (
`id` INTEGER PRIMARY KEY AUTOINCREMENT,
`phone` varchar(255) NOT NULL
)
END;
        $this->pdo = new \PDO("sqlite::memory:");
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec('DROP TABLE IF EXISTS `store`');
        $this->pdo->exec($sql);

    }

    public function testAddColumns()
    {
        $schema = new \Scheezy\Schema($this->pdo);
        $schema->loadFile(dirname(__FILE__) . '/schemas/store.yaml');
        $sql = $schema->__toString();

        $expected = <<<END
ALTER TABLE `store` ADD COLUMN `name` varchar(80);
ALTER TABLE `store` ADD COLUMN `active` tinyint(1);
ALTER TABLE `store` ADD COLUMN `user_count` INTEGER;
ALTER TABLE `store` ADD COLUMN `website` varchar(255)
END;

        $this->assertEquals($expected, $sql);
        $schema->synchronize();

    }

    /**
     * TODO: implement column dropping in SQLITE
     */
    public function testDropColumns()
    {
        $yaml = <<<END
table: store
columns:
    id:
END;

        $schema = new \Scheezy\Schema($this->pdo);
        $schema->loadString($yaml);
        $sql = $schema->__toString();

        $expected = <<<END
END;

        $this->assertEquals($expected, $sql);
        $schema->synchronize();

    }

    /**
     * TODO: implement column changes in SQLITE
     */
    public function testAlterColumns()
    {

        $sql = <<<END
CREATE TABLE `store` (
`id` INTEGER PRIMARY KEY AUTOINCREMENT,
`name` varchar(90) NOT NULL,
`active` int(11) NOT NULL,
`user_count` INTEGER NOT NULL,
`website` varchar(255) NOT NULL,
`phone` varchar(255) NOT NULL
)
END;

        $this->pdo->exec('DROP TABLE IF EXISTS `store`');
        $this->pdo->exec($sql);

        $schema = new \Scheezy\Schema($this->pdo);
        $schema->loadFile(dirname(__FILE__) . '/schemas/store.yaml');
        $sql = $schema->__toString();

        $expected = <<<END
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
        $sql = $schema->__toString();

        $expected = <<<END
ALTER TABLE `store` ADD COLUMN `email` varchar(255);
ALTER TABLE `store` ADD COLUMN `type` varchar(255);
CREATE UNIQUE INDEX `email` ON `store` (`email`);
CREATE INDEX `type` ON `store` (`type`)
END;

        $this->assertEquals($expected, $sql);
        $schema->synchronize();

    }
}
