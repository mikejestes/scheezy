<?php

namespace Scheezy\Tests;

class NoOpTest extends \PHPUnit_Framework_TestCase
{

    public function testNoOpMysql()
    {
        $pdo = new \PDO("mysql:host=localhost;dbname=scheezy", 'scheezy', '');
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->exec('DROP TABLE IF EXISTS `store_user_join`');
        $pdo->exec('DROP TABLE IF EXISTS `store`');

        $this->performTest($pdo);
    }

    public function zztestNoOpSqlite()
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
        $this->assertEquals('', $schema->toString());

        $schema = new \Scheezy\Schema($pdo);
        $schema->loadFile(dirname(__FILE__) . '/schemas/store.yaml');
        $this->assertEquals('', $schema->toString());
    }
}
