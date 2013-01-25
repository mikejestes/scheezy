<?php

namespace Scheezy\Table\Creator;

class Sqlite extends Mysql
{
    protected $yaml;
    protected $table;
    protected $indexes = array();

    public function createInteger($name, $options)
    {
        $extra = ' NOT NULL';

        if ($this->getOption($options, 'primary_key')) {
            $extra = ' PRIMARY KEY';
        }

        if ($this->getOption($options, 'auto_increment')) {
            $extra .= ' AUTOINCREMENT';
        }

        return "`$name` INTEGER$extra";
    }

    protected function addPrimaryKey($name, $options)
    {

    }
}
