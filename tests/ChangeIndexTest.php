<?php

namespace Scheezy\Tests;

class ChangeIndexTest extends ScheezyTestSuite
{

    public function testMysql()
    {
        $pdo = $this->pdo = $this->getMysqlPdo();
        $pdo->exec('DROP TABLE IF EXISTS `store_user_join`');

        $this->performChangeIndex($pdo);
    }

    protected function performChangeIndex($pdo)
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
        index: unique
END;

        $schema = new \Scheezy\Schema($pdo);
        $schema->loadString($yaml);

        $sql = <<<END
DROP INDEX `user_id` ON `store_user_join`;
CREATE UNIQUE INDEX `user_id` ON `store_user_join` (`user_id`)
END;

        $this->assertEquals($sql, $schema->__toString());
        $schema->synchronize();
    }
}
