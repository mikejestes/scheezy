<?php

namespace Scheezy\Tests;

class ScheezyTestSuite extends \PHPUnit_Framework_TestCase
{
    public function getMysqlPdo()
    {
        $pdo = new \PDO("mysql:host=localhost;dbname=scheezy_test", 'root', '');
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }
}
