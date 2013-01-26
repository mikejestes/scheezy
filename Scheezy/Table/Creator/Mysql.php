<?php

namespace Scheezy\Table\Creator;

class Mysql
{
    protected $yaml;
    protected $table;
    protected $indexes = array();

    public function __construct(\Scheezy\Table $table, $yaml)
    {
        $this->table = $table;
        $this->yaml = $yaml;
    }

    public function toString()
    {
        $sql = "CREATE TABLE `{$this->table->name}` (\n";

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

    public function createField($name, $options, $createMagic = true)
    {
        if ($name === 'id') {
            return $this->createId($name, (array)$options, $createMagic);
        }

        if (isset($options['index'])) {
            $this->indexes[] = array(
                'name' => $name,
                'type' => $options['index'],
            );
        }

        $type = $this->getOption($options, 'type', 'string');
        $fnc = 'create' . ucfirst($type);

        if (!method_exists($this, $fnc)) {
            throw new \Exception('Unknown Scheezy type: ' . $type);
        }
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

    public function createDatetime($name)
    {
        return "`$name` datetime NOT NULL";
    }

    public function createDate($name)
    {
        return "`$name` date NOT NULL";
    }

    public function createTimestamp($name)
    {
        return "`$name` timestamp NOT NULL";
    }

    public function createText($name)
    {
        return "`$name` text NOT NULL";
    }

    public function createDecimal($name, $options)
    {
        $precision = $this->getOption($options, 'precision', 10);
        $scale = $this->getOption($options, 'scale', 2);
        return "`$name` decimal($precision,$scale) NOT NULL";
    }

    public function createIndex($options)
    {
        if ($options['type'] === true) {
            $options['type'] = 'INDEX';
        }

        $options['type'] = strtoupper($options['type']);

        return "{$options['type']} (`{$options['name']}`)";
    }

    protected function getOption($options, $key, $default = null)
    {
        if (!isset($options[$key])) {
            return $default;
        }

        return $options[$key];
    }

    protected function addPrimaryKey($name)
    {
        $this->indexes[] = array(
            'name' => $name,
            'type' => 'PRIMARY KEY',
        );
    }

    public function createId($name, $options, $createMagic = true)
    {

        if ($createMagic) {
            $this->addPrimaryKey($name, $options);
        }

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
