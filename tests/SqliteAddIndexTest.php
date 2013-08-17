<?php

namespace Scheezy\Tests;

class SqlAddIndexTest extends MysqlAddIndexTest
{

    public function setUp()
    {
        $this->pdo = new \PDO("sqlite::memory:");
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec('DROP TABLE IF EXISTS `store_user_join`');
    }


    /**
     * @expectedException Exception
     * @expectedExceptionMessage SQLSTATE[HY000]: General error: 1 Cannot add a PRIMARY KEY column
     */
    public function testAddPrimaryKey()
    {
        parent::testAddPrimaryKey();
    }
}
