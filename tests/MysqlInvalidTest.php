<?php

namespace Scheezy\Tests;

class MysqlInvalidTest extends ScheezyTestSuite
{

    protected function setUp(): void
    {
        $this->pdo = $this->getMysqlPdo();
        $this->pdo->exec('DROP TABLE IF EXISTS `store`');
        $this->pdo->exec('DROP TABLE IF EXISTS `store_user_join`');
    }

    public function testMissingEnumValues()
    {
        $this->expectException(\Exception::class);

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
