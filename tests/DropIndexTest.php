<?php

namespace Scheezy\Tests;

class DropIndexTest extends ScheezyTestSuite
{

    public function testMysql()
    {
        $pdo = $this->pdo = $this->getMysqlPdo();
        $pdo->exec('DROP TABLE IF EXISTS `store_user_join`');

        $this->performDropIndex($pdo);
    }

    protected function performDropIndex($pdo)
    {
        $schema = new \Scheezy\Schema($pdo);
        $schema->loadFile(dirname(__FILE__) . '/schemas/store_user_join.yaml');
        $schema->synchronize();


        $yaml = <<<END
table: store_user_join
columns:
    store_id:
        type: integer
        index: true
    user_id:
        type: integer
END;

        $schema = new \Scheezy\Schema($pdo);
        $schema->loadString($yaml);

        $sql = <<<END
DROP INDEX `user_id` ON `store_user_join`
END;

        $this->assertEquals($sql, $schema->__toString());
        $schema->synchronize();
    }
}
