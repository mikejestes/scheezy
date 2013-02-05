<?php

namespace Scheezy\Table\Creator;

class Mysql
{
    protected $yaml;
    protected $table;

    protected $mainCommands = array();
    protected $postCommands = array();

    public function __construct(\Scheezy\Table $table, $yaml)
    {
        $this->table = $table;
        $this->yaml = $yaml;
    }

    public function __toString()
    {
        $sql = "CREATE TABLE `{$this->table->name}` (\n";

        foreach ($this->yaml['columns'] as $fieldName => $fieldOptions) {
            $this->mainCommands[] = $this->table->createField($fieldName, $fieldOptions);
        }

        foreach ($this->table->indexes() as $index) {
            $this->postCommands[] = $this->createIndex($index);
        }

        return $sql . $this->combineCommands();
    }

    public function removeEmpty()
    {
        $this->mainCommands = array_filter(
            $this->mainCommands,
            function ($line) {
                return strlen($line) > 0;
            }
        );
        $this->postCommands = array_filter(
            $this->postCommands,
            function ($line) {
                return strlen($line) > 0;
            }
        );
    }

    public function combineCommands()
    {
        $this->removeEmpty();
        return implode(",\n", array_merge($this->mainCommands, $this->postCommands)) . "\n)";
    }

    public function createIndex($index)
    {
        return "{$index->type} (`{$index->name}`)";
    }
}
