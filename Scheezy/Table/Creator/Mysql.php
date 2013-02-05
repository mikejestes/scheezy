<?php

namespace Scheezy\Table\Creator;

class Mysql
{
    protected $yaml;
    protected $table;

    public function __construct(\Scheezy\Table $table, $yaml)
    {
        $this->table = $table;
        $this->yaml = $yaml;
    }

    public function __toString()
    {
        $creations = array();
        $postCommands = array();

        $sql = "CREATE TABLE `{$this->table->name}` (\n";

        foreach ($this->yaml['columns'] as $fieldName => $fieldOptions) {
            $creations[] = $this->table->createField($fieldName, $fieldOptions);
        }

        foreach ($this->table->indexes as $indexOptions) {
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

        return $sql . $this->combineCommands($creations, $postCommands);

    }

    public function combineCommands($commands, $postCommands)
    {
        return implode(",\n", array_merge($commands, $postCommands)) . "\n)";
    }

    public function createIndex($options)
    {
        if ($options['type'] === true) {
            $options['type'] = 'INDEX';
        }

        $options['type'] = strtoupper($options['type']);

        return "{$options['type']} (`{$options['name']}`)";
    }
}
