<?php

namespace Scheezy\Tests;

class MysqlInvalidTest extends ScheezyTestSuite
{

    public function setUp()
    {
        $this->pdo = $this->getMysqlPdo();
        $this->pdo->exec('DROP TABLE IF EXISTS `store`');
        $this->pdo->exec('DROP TABLE IF EXISTS `store_user_join`');
    }


    /**
     * @expectedException Exception
     */
    public function testMissingEnumValues()
    {
        $yaml = <<<END
table: store
columns:
    id:
    status:
        type: enum
END;

        $schema = new \Scheezy\Schema($this->pdo);
        $schema->loadString($yaml);
        $schema->__toString();
    }
}
