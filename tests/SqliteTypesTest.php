<?php

namespace Scheezy\Tests;

class SqliteTypesTest extends MysqlTypesTest
{

    public function setUp()
    {
        $this->pdo = new \PDO('sqlite::memory:');
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    protected function expectedSql()
    {
        return <<<END
CREATE TABLE `types` (
`id` INTEGER PRIMARY KEY AUTOINCREMENT,
`created` datetime NOT NULL,
`updated` timestamp NOT NULL,
`calendar` date NOT NULL,
`paragraph` text NOT NULL,
`price` decimal(10,2) NOT NULL,
`default_num` INTEGER NOT NULL DEFAULT 42,
`latitude` decimal(9,6) NOT NULL,
`record_year` year NOT NULL,
`record_time` time NOT NULL
)
END;
    }
}
