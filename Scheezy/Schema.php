<?php

namespace Scheezy;

class Schema
{
    private $yaml;

    public function __construct($file)
    {
        $this->yaml = spyc_load_file($file);
    }

    public function getTableName()
    {
        $keys = array_keys($this->yaml);
        return array_shift($keys);
    }

    public function toString($type)
    {
        $class = 'Scheezy\\Table\\Creator\\' . ucfirst($type);
        $modifier = new $class($this->getTableName(), $this->yaml);
        return $modifier->toString();
    }

    public function execute(\PDO $connection)
    {
        $sql = $this->toString($connection->getAttribute(\PDO::ATTR_DRIVER_NAME));
        $connection->exec($sql);
    }
}
