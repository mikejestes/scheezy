<?php

namespace Scheezy\Table;

class Mysql implements \Scheezy\Table
{

    public $name;

    public function __construct($name, \PDO $connection)
    {
        $this->name = $name;
        $this->connection = $connection;
    }

    public function exists($type)
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

    public function columnExists($column)
    {
        $result = $this->connection->query("desc {$this->name}");
        $columns = $result->fetchAll(\PDO::FETCH_COLUMN, 0);
        return in_array($column, $columns);
    }
}