<?php

namespace Scheezy\Table\Creator;

class Sqlite extends Mysql
{
    public function toString()
    {
        $creations = array();
        $postCommands = array();

        $sql = "CREATE TABLE `{$this->table->name}` (\n";

        foreach ($this->yaml['columns'] as $fieldName => $fieldOptions) {
            $creations[] = $this->createField($fieldName, $fieldOptions);
        }

        foreach ($this->indexes as $indexOptions) {
            $postCommands[] = $this->createIndex($indexOptions);
        }

        $creations = array_filter(
            $creations,
            function ($line) {
                return strlen($line) > 0;
            }
        );
        $postCommands = array_filter(
            $postCommands,
            function ($line) {
                return strlen($line) > 0;
            }
        );

        $sql .= implode(";\n", array_merge(array(implode(",\n", $creations) . "\n)"), $postCommands));

        return $sql;
    }

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

    protected function addPrimaryKey($name)
    {

    }
}
