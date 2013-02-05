<?php

namespace Scheezy\Table;

class Mysql extends \Scheezy\Table
{
    public $name;
    private $connection;

    public function __construct($name, \PDO $connection)
    {
        $this->name = $name;
        $this->connection = $connection;
        $this->definitions = new \Scheezy\Column\Definition('mysql');
    }

    public function createIndex($index)
    {
        return "{$index->type} (`{$index->name}`) ON `{$this->name}`";
    }

    public function exists()
    {
        $sql = "show tables";
        $result = $this->connection->query($sql);
        $tables = $result->fetchAll(\PDO::FETCH_COLUMN, 0);

        return in_array($this->name, $tables);
    }

    public function columns()
    {
        $result = $this->connection->query("desc {$this->name}");
        return $result->fetchAll(\PDO::FETCH_CLASS, '\Scheezy\Column');
    }

    public function columnDetail($column)
    {
        $result = $this->connection->query("desc {$this->name}");
        $columns = $result->fetchAll(\PDO::FETCH_CLASS, '\Scheezy\Column');
        $theColumn = array_filter(
            $columns,
            function ($row) use ($column) {
                if ($row->Field == $column) {
                    return true;
                }
            }
        );
        return array_shift($theColumn);
    }

    public function currentIndexes()
    {
        $sql = "SHOW INDEXES IN `$this->name`";
        $result = $this->connection->query($sql);
        return $result->fetchAll(\PDO::FETCH_CLASS, '\Scheezy\Index');
    }

    public function indexDetail($index)
    {
        $sql = "SHOW INDEXES IN `$this->name`";
        $result = $this->connection->query($sql);
        $indexes = $result->fetchAll(\PDO::FETCH_CLASS, '\Scheezy\Index');

        $exists = array_filter(
            $indexes,
            function ($eachIndex) use ($index) {
                return $eachIndex->field == $index->field;
            }
        );
        return count($exists) > 0 ? array_shift($exists) : false;
    }
}
