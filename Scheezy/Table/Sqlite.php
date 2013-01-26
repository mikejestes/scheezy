<?php

namespace Scheezy\Table;

class Sqlite implements \Scheezy\Table
{
    public $name;
    private $connection;

    public function __construct($name, \PDO $connection)
    {
        $this->name = $name;
        $this->connection = $connection;
    }

    public function exists()
    {
        $sql = "SELECT name FROM sqlite_master WHERE type = 'table'";
        $result = $this->connection->query($sql);
        $tables = $result->fetchAll(\PDO::FETCH_COLUMN, 0);

        return in_array($this->name, $tables);
    }

    public function columns()
    {
        $sql = "PRAGMA table_info('{$this->name}')";
        $result = $this->connection->query($sql);
        return $result->fetchAll(\PDO::FETCH_CLASS, '\Scheezy\Column');
    }

    public function columnDetail($column)
    {
        $columns = $this->columns();
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

    public function columnExists($column)
    {
        $sql = "PRAGMA table_info('{$this->name}')";
        $result = $this->connection->query($sql);
        $columns = $result->fetchAll(\PDO::FETCH_COLUMN, 1);
        return in_array($column, $columns);
    }
}
