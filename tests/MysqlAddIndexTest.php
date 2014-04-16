<?php

namespace Scheezy\Tests;

class MysqlAddIndexTest extends ScheezyTestSuite
{
    public function setUp()
    {
        $this->pdo = $this->getMysqlPdo();
        $this->pdo->exec('DROP TABLE IF EXISTS `store_user_join`');
    }

    public function testAddTwoIndexes()
    {

        $yaml = <<<END
table: indexes
columns:
    id:
    a:
        index: true
    b:
        index: true
    c:
    d:
END;

        $schema = new \Scheezy\Schema($this->pdo);
        $schema->loadString($yaml);
        $schema->synchronize();

        $schema = new \Scheezy\Schema($this->pdo);
        $schema->loadFile(dirname(__FILE__) . '/schemas/indexes.yaml');

        $sql = <<<END
CREATE INDEX `c` ON `indexes` (`c`);
CREATE INDEX `d` ON `indexes` (`d`)
END;

        $this->assertEquals($sql, $schema->__toString());
        $schema->synchronize();
    }

    public function testAddPrimaryKey()
    {

        $yaml = <<<END
table: pk
columns:
    a:
        index: true
    created:
        type: datetime
END;

        $schema = new \Scheezy\Schema($this->pdo);
        $schema->loadString($yaml);
        $schema->synchronize();

        $yaml2 = <<<END
table: pk
columns:
    id:
    a:
        index: true
    created:
        type: datetime
END;

        $schema = new \Scheezy\Schema($this->pdo);
        $schema->loadString($yaml2);

        $sql = <<<END
ALTER TABLE `pk`
ADD COLUMN `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY
END;

        $this->assertEquals($sql, $schema->__toString());
        $schema->synchronize();
    }
}
