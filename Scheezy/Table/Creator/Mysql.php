<?php

namespace Scheezy\Table\Creator;

class Mysql
{
    protected $yaml;
    protected $table;

    public function __construct($table, $yaml)
    {
        $this->table = $table;
        $this->yaml = $yaml;
    }

    public function toString()
    {
        $sql = "CREATE TABLE `{$this->table}` (\n";

        $this->injectPrimaryKey($this->table);

        foreach ($this->yaml[$this->table] as $fieldName => $fieldOptions) {
            $sql .= $this->createField($fieldName, $fieldOptions);
            $sql .= ",\n";
        }

        $sql = rtrim($sql, ",\n");

        $sql .= "\n)";

        return $sql;
    }

    public function createField($name, $options)
    {
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

    public function createKey($name, $options)
    {
        return "PRIMARY KEY (`{$options['name']}`)";
    }

    protected function getOption($options, $key, $default = null)
    {
        if (!isset($options[$key])) {
            return $default;
        }

        return $options[$key];
    }

    protected function injectPrimaryKey($table)
    {

        if (!isset($this->yaml[$table]['id'])) {
            $this->yaml[$table] = array_merge(
                array('id' => array(
                    'type' => 'integer',
                    'auto_increment' => true,
                )),
                $this->yaml[$table]
            );
        }

        if (!isset($this->yaml[$table]['primary_key'])) {
            $this->yaml[$table]['primary_key'] = array(
                'type' => 'key',
                'name' => 'id',
            );
        }
    }
}
