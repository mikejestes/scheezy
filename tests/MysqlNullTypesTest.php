<?php

namespace Scheezy\Tests;

class MysqlNullTypesTest extends ScheezyTestSuite
{

    public function setUp()
    {
        $this->pdo = $this->getMysqlPdo();
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function testTypes()
    {
        $yaml = <<<END
table: null_types
columns:
    id:
    created:
        type: datetime
        allow_null: true
    updated:
        type: timestamp
        allow_null: true
    calendar:
        type: date
        allow_null: true
    paragraph:
        type: text
        allow_null: true
    title:
        allow_null: true
    price:
        type: decimal
        allow_null: true
    default_num:
        type: integer
        default: 42
        allow_null: true
    latitude:
        type: decimal
        precision: 9
        scale: 6
        allow_null: true
    record_year:
        type: year
        allow_null: true
    record_time:
        type: time
        allow_null: true
END;

        $schema = new \Scheezy\Schema($this->pdo);
        $schema->loadString($yaml);

        $this->assertEquals($this->expectedSql(), $schema->__toString());
        $schema->synchronize();
    }

    protected function expectedSql()
    {
        return <<<END
CREATE TABLE `null_types` (
`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
`created` datetime,
`updated` timestamp,
`calendar` date,
`paragraph` text,
`title` varchar(255),
`price` decimal(10,2),
`default_num` int(11) DEFAULT 42,
`latitude` decimal(9,6),
`record_year` year,
`record_time` time
)
END;
    }
}
