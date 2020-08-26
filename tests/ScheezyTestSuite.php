<?php

namespace Scheezy\Tests;

class ScheezyTestSuite extends \PHPUnit\Framework\TestCase
{
    public function getMysqlPdo()
    {
        $pdo = new \PDO("mysql:host=127.0.0.1;dbname=scheezy_test", 'root', '');
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }
}
