<?php

namespace Scheezy\Table\Modifier;

class Sqlite extends General
{
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
}
