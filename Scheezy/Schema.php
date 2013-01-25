<?php

namespace Scheezy;

class Schema
{
    private $yaml;

    public function __construct($structure, \PDO $connection)
    {
        if (is_file($structure)) {
            $this->yaml = spyc_load_file($structure);
        } else {
            $this->yaml = spyc_load($structure);
        }
        $this->connection = $connection;
    }

    public function getTableName()
    {
        return $this->yaml['table'];
    }

    public function getTable()
    {
        $type = $this->connection->getAttribute(\PDO::ATTR_DRIVER_NAME);
        $class = 'Scheezy\\Table\\' . ucfirst($type);
        return new $class($this->getTableName(), $this->connection);
    }

    public function toString()
    {
        $table = $this->getTable();
        $type = $this->connection->getAttribute(\PDO::ATTR_DRIVER_NAME);

        if ($table->exists($type)) {
            $prefix = 'Scheezy\\Table\\Modifier\\';
        } else {
            $prefix = 'Scheezy\\Table\\Creator\\';
        }

        $class = $prefix . ucfirst($type);
        $modifier = new $class($table, $this->yaml);
        return $modifier->toString();
    }

    public function execute()
    {
        $sql = $this->toString($this->connection->getAttribute(\PDO::ATTR_DRIVER_NAME));
        $this->connection->exec($sql);
    }
}
