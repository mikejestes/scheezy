<?php

namespace Scheezy\Tests;

class AddIndexTest extends ScheezyTestSuite
{

    public function testMysql()
    {
        $pdo = $this->pdo = $this->getMysqlPdo();
        $pdo->exec('DROP TABLE IF EXISTS `store_user_join`');

        $this->performAddTwoIndexes($pdo);
    }

    public function testSqlite()
    {
        $pdo = new \PDO("sqlite::memory:");
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->exec('DROP TABLE IF EXISTS `store_user_join`');

        $this->performAddTwoIndexes($pdo);
    }

    protected function performAddTwoIndexes($pdo)
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

        $schema = new \Scheezy\Schema($pdo);
        $schema->loadString($yaml);
        $schema->synchronize();

        $schema = new \Scheezy\Schema($pdo);
        $schema->loadFile(dirname(__FILE__) . '/schemas/indexes.yaml');

        $sql = <<<END
CREATE INDEX `c` ON `indexes` (`c`);
CREATE INDEX `d` ON `indexes` (`d`)
END;

        $this->assertEquals($sql, $schema->__toString());
        $schema->synchronize();
    }
}
