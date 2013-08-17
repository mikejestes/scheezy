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

    public function createIndex($index)
    {
        $type = $index->getType();
        return "CREATE $type `{$index->name}` ON `{$this->name}` (`{$index->field}`)";
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

    public function currentIndexes()
    {
        $sql = "PRAGMA INDEX_LIST('$this->name')";
        $result = $this->connection->query($sql);
        return $result->fetchAll(\PDO::FETCH_CLASS, '\Scheezy\Index');
    }

    public function indexDetail($index)
    {
        $sql = "PRAGMA INDEX_LIST('$this->name')";
        $result = $this->connection->query($sql);
        $indexes = $result->fetchAll(\PDO::FETCH_CLASS, '\Scheezy\Index');

        $exists = array_filter(
            $indexes,
            function ($indexData) use ($index) {
                return $indexData->name == $index->name;
            }
        );
        return count($exists) > 0 ? array_shift($exists) : false;
    }
}
