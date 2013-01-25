<?php

namespace Scheezy;

class Column
{
    public $Field;
    public $Type;
    public $Null;
    public $Extra;

    public function __set($key, $value)
    {
        switch ($key) {
            case 'name':
                $key = 'Field';
                break;
            case 'type':
                $key = 'Type';
                break;
            case 'notnull':
                $key = 'Null';
                if ($value) {
                    $value = 'NO';
                }
                break;
            case 'pk':
                $key = 'PrimaryKey';
                if ($value) {
                    $this->Extra = 'auto_increment';
                }
                break;

        }

        $this->$key = $value;
    }
}
