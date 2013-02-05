<?php

namespace Scheezy\Tests;

class MysqlTypesTest extends ScheezyTestSuite
{

    public function setUp()
    {
        $this->pdo = $this->getMysqlPdo();
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function testTypes()
    {
        $yaml = <<<END
table: types
columns:
    id:
    created:
        type: datetime
    updated:
        type: timestamp
    calendar:
        type: date
    paragraph:
        type: text
    price:
        type: decimal
    latitude:
        type: decimal
        precision: 9
        scale: 6
END;

        $schema = new \Scheezy\Schema($this->pdo);
        $schema->loadString($yaml);

        $this->assertEquals($this->expectedSql(), $schema->__toString());
        $schema->synchronize();
    }

    protected function expectedSql()
    {
        return <<<END
CREATE TABLE `types` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`created` datetime NOT NULL,
`updated` timestamp NOT NULL,
`calendar` date NOT NULL,
`paragraph` text NOT NULL,
`price` decimal(10,2) NOT NULL,
`latitude` decimal(9,6) NOT NULL,
PRIMARY KEY (`id`)
)
END;
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Unknown Scheezy type: flabbergasted
     */
    public function testExceptionForUnknownType()
    {
        $yaml = <<<END
table: oops
columns:
    id:
    created:
        type: flabbergasted
END;

        $schema = new \Scheezy\Schema($this->pdo);
        $schema->loadString($yaml);
        $schema->__toString();
    }
}
