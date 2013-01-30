<?php

namespace Scheezy\Table;

class Sqlite extends \Scheezy\Table
{
    public $name;
    private $connection;

    public function __construct($name, \PDO $connection)
    {
        $this->name = $name;
        $this->connection = $connection;
        $this->definitions = new \Scheezy\Column\Definition('sqlite');
    }

    public function createIndex($options)
    {
        if ($options['type'] === true) {
            $options['type'] = '';
        }

        $options['type'] = strtoupper($options['type']);
        if ($options['type']) {
            $options['type'] .= ' ';
        }

        return "CREATE {$options['type']}INDEX `{$options['name']}` ON `{$this->name}` (`{$options['name']}`)";
    }

    public function addPrimaryKey($name)
    {

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

    public function indexes()
    {
        $sql = "PRAGMA INDEX_LIST('$this->name')";
        $result = $this->connection->query($sql);
        return $result->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function indexExists($name)
    {
        $sql = "PRAGMA INDEX_LIST('$this->name')";
        $result = $this->connection->query($sql);
        $indexes = $result->fetchAll(\PDO::FETCH_ASSOC);

        $exists = array_filter(
            $indexes,
            function ($indexData) use ($name) {
                return $indexData['name'] == $name;
            }
        );
        return count($exists) > 0;
    }
}
