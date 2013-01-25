<?php

namespace Scheezy\Table\Creator;

class Sqlite extends Mysql
{
    protected $yaml;
    protected $table;

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

    protected function injectPrimaryKey($table)
    {

        if (!isset($this->yaml[$table]['id'])) {
            $this->yaml[$table] = array_merge(
                array('id' => array(
                    'type' => 'integer',
                    'auto_increment' => true,
                    'primary_key' => true,
                )),
                $this->yaml[$table]
            );
        }

    }
}
