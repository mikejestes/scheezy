<?php

namespace Scheezy\Table\Creator;

class Mysql
{
    protected $yaml;
    protected $table;
    protected $indexes = array();

    public function __construct($table, $yaml)
    {
        $this->table = $table;
        $this->yaml = $yaml;
    }

    public function toString()
    {
        $sql = "CREATE TABLE `{$this->table}` (\n";

        foreach ($this->yaml['columns'] as $fieldName => $fieldOptions) {
            $sql .= $this->createField($fieldName, $fieldOptions);
            $sql .= ",\n";
        }

        foreach ($this->indexes as $indexOptions) {
            $sql .= $this->createIndex($indexOptions);
            $sql .= ",\n";
        }

        $sql = rtrim($sql, ",\n");

        $sql .= "\n)";

        return $sql;
    }

    public function createField($name, $options)
    {
        if ($name === 'id') {
            return $this->createId($name, (array)$options);
        }

        if (isset($options['index'])) {
            $this->indexes[] = array(
                'name' => $name,
                'type' => 'INDEX',
            );
        }

        $type = $this->getOption($options, 'type', 'string');
        $fnc = 'create' . ucfirst($type);
        return $this->$fnc($name, $options);
    }

    public function createString($name, $options)
    {
        $length = $this->getOption($options, 'length', 255);
        return "`$name` varchar($length) NOT NULL";
    }

    public function createInteger($name, $options)
    {
        $length = $this->getOption($options, 'length', 11);
        $autoIncrement = $this->getOption($options, 'auto_increment');
        $extra = $autoIncrement ? ' AUTO_INCREMENT' : '';
        return "`$name` int($length) NOT NULL$extra";
    }

    public function createBoolean($name)
    {
        return "`$name` tinyint(1) NOT NULL";
    }

    public function createIndex($options)
    {
        return "{$options['type']} (`{$options['name']}`)";
    }

    protected function getOption($options, $key, $default = null)
    {
        if (!isset($options[$key])) {
            return $default;
        }

        return $options[$key];
    }

    protected function addPrimaryKey($name, $options)
    {
        $this->indexes[] = array(
            'name' => $name,
            'type' => 'PRIMARY KEY',
        );
    }

    public function createId($name, $options)
    {

        $this->addPrimaryKey($name, $options);

        $options = array_merge(
            $options,
            array(
                'auto_increment' => true,
                'primary_key' => true,
            )
        );

        return $this->createInteger($name, $options);

    }
}
