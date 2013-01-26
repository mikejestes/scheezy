<?php

namespace Scheezy\Table\Modifier;

class Sqlite extends Mysql
{
    protected function combineCommands($modifications, $postCommands)
    {
        $commands = array();

        $commands = array_map(
            function ($line) {
                $line = str_replace(' NOT NULL', '', $line);
                return "ALTER TABLE `{$this->table->name}` $line";
            },
            $modifications
        );

        $sql = implode(";\n", array_merge($commands, $postCommands));

        return $sql;
    }

    public function modifyField($name, $options)
    {
        $currentColumnDetails = $this->table->columnDetail($name);
        $newLine = $this->createField($name, $options, false);
        $currentLine = "`$name` {$currentColumnDetails->Type}";
        if ($currentColumnDetails->Null == 'NO') {
            $currentLine .= " NOT NULL";
        }
        if ($currentColumnDetails->PrimaryKey) {
            $currentLine .= " PRIMARY KEY";
        }
        if ($currentColumnDetails->Extra == 'auto_increment') {
            $currentLine .= " AUTOINCREMENT";
        }

        if ($newLine != $currentLine) {
            return 'CHANGE ' . $newLine;
        }
    }

    public function createInteger($name, $options)
    {
        $extra = ' NOT NULL';

        // if ($this->getOption($options, 'primary_key')) {
        //     $extra = ' PRIMARY KEY';
        // }

        if ($this->getOption($options, 'auto_increment')) {
            $extra = ' PRIMARY KEY AUTOINCREMENT';
        }

        return "`$name` INTEGER$extra";
    }

    public function createIndex($options)
    {
        if ($options['type'] === true) {
            $options['type'] = '';
        }

        $options['type'] = strtoupper($options['type']);
        if ($options['type']) {
            $options['type'] .= ' ';
        }

        return "CREATE {$options['type']}INDEX `{$options['name']}` ON `{$this->table->name}` (`{$options['name']}`)";
    }

    public function dropIndex($name)
    {
        return "DROP INDEX `$name`";
    }
}
