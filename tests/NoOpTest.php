<?php

namespace Scheezy\Tests;

class NoOpTest extends ScheezyTestSuite
{

    public function testNoOpMysql()
    {
        $pdo = $this->getMysqlPdo();
        $pdo->exec('DROP TABLE IF EXISTS `store_user_join`');
        $pdo->exec('DROP TABLE IF EXISTS `store`');

        $this->performTest($pdo);
    }

    public function testNoOpSqlite()
    {
        $pdo = new \PDO('sqlite::memory:');
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->exec('DROP TABLE IF EXISTS `store_user_join`');
        $pdo->exec('DROP TABLE IF EXISTS `store`');

        $this->performTest($pdo);
    }

    protected function performTest($pdo)
    {
        $schema = new \Scheezy\Schema($pdo);
        $schema->loadFile(dirname(__FILE__) . '/schemas/store_user_join.yaml');
        $schema->synchronize();

        $schema = new \Scheezy\Schema($pdo);
        $schema->loadFile(dirname(__FILE__) . '/schemas/store.yaml');
        $schema->synchronize();


        // these calls should result in no changes generated
        $schema = new \Scheezy\Schema($pdo);
        $schema->loadFile(dirname(__FILE__) . '/schemas/store_user_join.yaml');
        $this->assertEquals('', $schema->__toString());
        $schema->synchronize();

        $schema = new \Scheezy\Schema($pdo);
        $schema->loadFile(dirname(__FILE__) . '/schemas/store.yaml');
        $this->assertEquals('', $schema->__toString());
        $schema->synchronize();
    }
}
