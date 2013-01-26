<?php

namespace Scheezy\Tests;

class DirectoryRunnerTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {

        $this->pdo = new \PDO("mysql:host=localhost;dbname=scheezy", 'scheezy', '');
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec('DROP TABLE IF EXISTS `store`');
        $this->pdo->exec('DROP TABLE IF EXISTS `store_user_join`');

    }

    public function testLoadDirectory()
    {
        $schema = new \Scheezy\Schema($this->pdo);
        $schema->loadDirectory(dirname(__FILE__) . '/schemas/');
        $schema->synchronize();

        $table = new \Scheezy\Table\Mysql('store', $this->pdo);
        $this->assertTrue($table->exists());
        $table = new \Scheezy\Table\Mysql('store_user_join', $this->pdo);
        $this->assertTrue($table->exists());
    }

    public function testLoadDirectoryNoSlash()
    {
        $schema = new \Scheezy\Schema($this->pdo);
        $schema->loadDirectory(dirname(__FILE__) . '/schemas');
        $schema->synchronize();

        $table = new \Scheezy\Table\Mysql('store', $this->pdo);
        $this->assertTrue($table->exists());
        $table = new \Scheezy\Table\Mysql('store_user_join', $this->pdo);
        $this->assertTrue($table->exists());
    }

    public function testLoadGlob()
    {
        $glob = glob(dirname(__FILE__) . '/schemas/*.yaml');
        $schema = new \Scheezy\Schema($this->pdo);
        $schema->loadGlob($glob);
        $schema->synchronize();

        $table = new \Scheezy\Table\Mysql('store', $this->pdo);
        $this->assertTrue($table->exists());
        $table = new \Scheezy\Table\Mysql('store_user_join', $this->pdo);
        $this->assertTrue($table->exists());

    }
}
