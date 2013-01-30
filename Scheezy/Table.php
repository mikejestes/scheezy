<?php

namespace Scheezy;

abstract class Table
{
    public $indexes = array();

    public function createField($name, $options)
    {
        if ($name === 'id') {
            $this->addPrimaryKey($name);
            return $this->definitions->createId($name, (array)$options);
        }

        if (isset($options['index'])) {
            $this->indexes[] = array(
                'name' => $name,
                'type' => $options['index'],
            );
        }

        $type = $this->definitions->getOption($options, 'type', 'string');
        $fnc = 'make' . ucfirst($type);

        return $this->definitions->$fnc($name, $options);
    }

    protected function addPrimaryKey($name)
    {
        $this->indexes[] = array(
            'name' => $name,
            'type' => 'PRIMARY KEY',
        );
    }

    abstract public function exists();
    abstract public function columns();
    abstract public function columnDetail($column);
    abstract public function columnExists($column);
    abstract public function indexExists($name);
}
