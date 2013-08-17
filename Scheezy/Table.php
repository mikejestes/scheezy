<?php

namespace Scheezy;

abstract class Table
{
    protected $indexes = array();

    public function createField($name, $options)
    {
        if ($name === 'id') {
            return $this->definitions->createId($name, (array)$options);
        }

        if (isset($options['index'])) {
            $this->indexes[] = new Index(
                array(
                    'name' => $name,
                    'field' => $name,
                    'type' => $options['index'],
                )
            );
        }

        $type = $this->definitions->getOption($options, 'type', 'string');
        $fnc = 'make' . ucfirst($type);

        return $this->definitions->$fnc($name, $options);
    }

    public function indexes()
    {
        return $this->indexes;
    }

    public function indexExists($index)
    {
        return !!$this->indexDetail($index);
    }

    public function columnExists($column)
    {
        return !!$this->columnDetail($column);
    }

    abstract public function exists();
    abstract public function columns();
    abstract public function columnDetail($column);
    abstract public function indexDetail($index);
    abstract public function currentIndexes();
}
