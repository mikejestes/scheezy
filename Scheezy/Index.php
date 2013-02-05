<?php

namespace Scheezy;

class Index
{
    public $field;
    public $unique;
    public $sequence;
    public $table;
    public $type = 'INDEX';

    public function __construct($values = array())
    {
        foreach ($values as $key => $value) {
            $this->__set($key, $value);
        }
    }

    public function __set($key, $value)
    {
        $key = strtolower($key);

        switch ($key) {
            case 'column_name':
                $key = 'field';
                break;
            case 'key_name':
            case 'name':
                $key = 'name';
                if ($value == 'PRIMARY') {
                    $this->type = 'PRIMARY KEY';
                }
                if (!$this->field) {
                    $this->field = $value;
                }
                break;
            case 'type':
                if ($value == '1') {
                    $value = 'INDEX';
                }
                $value = strtoupper($value);
                if ($value == 'UNIQUE') {
                    $this->unique = true;
                }
                break;
            case 'non_unique':
                if ($value) {
                    $this->unique = false;
                }
                break;

        }

        $this->$key = $value;
    }

    public function getType()
    {
        if ($this->unique) {
            return 'UNIQUE INDEX';
        }

        return $this->type;
    }
}
