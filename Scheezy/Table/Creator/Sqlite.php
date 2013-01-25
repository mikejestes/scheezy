<?php

namespace Scheezy\Table\Creator;

class Sqlite extends General
{
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
