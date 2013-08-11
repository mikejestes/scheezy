<?php

namespace Scheezy\Table\Modifier;

class Mysql extends \Scheezy\Table\Creator\Mysql
{
    protected $yaml;
    protected $indexes = array();

    public function __construct(\Scheezy\Table $table, $yaml)
    {
        $this->table = $table;
        $this->yaml = $yaml;
    }

    public function __toString()
    {
        // see if columns need to be dropped
        foreach ($this->table->columns() as $column) {
            if (!in_array($column->Field, array_keys($this->yaml['columns']))) {
                $this->mainCommands[] = $this->dropField($column->Field);
            }
        }

        // see if columns need to be modified or added
        foreach ($this->yaml['columns'] as $fieldName => $fieldOptions) {
            $this->mainCommands[] = $this->insureField($fieldName, $fieldOptions);
        }

        // see what indexes need to be added
        $desiredIndexes = array();
        foreach ($this->table->indexes() as $index) {
            $desiredIndexes[] = $index->field;
            $this->postCommands[] = $this->insureIndex($index);
        }

        // see what indexes need to be dropped
        foreach ($this->table->currentIndexes() as $index) {
            if (!in_array($index->field, $desiredIndexes) && $index->type != 'PRIMARY KEY') {
                $this->postCommands[] = $this->dropIndex($index);
            }
        }

        return $this->combineCommands();
    }

    public function combineCommands()
    {
        $this->removeEmpty();
        $commands = array();

        if (count($this->mainCommands)) {
            $action = implode(",\n", $this->mainCommands);
            if (count($this->mainCommands) > 0) {
                $action = "\n$action";
            }
            $commands[] = "ALTER TABLE `{$this->table->name}`$action";
        }

        $sql = implode(";\n", array_merge($commands, $this->postCommands));

        return $sql;
    }

    public function insureField($name, $options)
    {
        if ($this->table->columnExists($name)) {
            return $this->modifyField($name, $options);
        } else {
            return 'ADD COLUMN ' . $this->table->createField($name, $options);
        }
    }

    public function insureIndex($index)
    {
        if ($this->table->indexExists($index)) {
            return $this->modifyIndex($index);
        } elseif ($index->type != 'PRIMARY KEY') {
            return $this->createIndex($index);
        }
    }

    public function modifyField($name, $options)
    {
        $currentColumnDetails = $this->table->columnDetail($name);
        $newLine = $this->table->createField($name, $options, false);
        $currentLine = "`$name` {$currentColumnDetails->Type}";
        if ($currentColumnDetails->Null == 'NO') {
            $currentLine .= " NOT NULL";
        }
        if ($currentColumnDetails->Extra == 'auto_increment') {
            $currentLine .= " AUTO_INCREMENT";
        }
        if ($newLine != $currentLine) {
            return "CHANGE `$name` " . $newLine;
        }
    }

    public function modifyIndex($index)
    {
        $match = true;

        $currentIndex = $this->table->indexDetail($index);

        if ($currentIndex->getType() != $index->getType()) {
            $match = false;
        }

        if (!$match) {
            $this->postCommands[] = $this->dropIndex($currentIndex);
            $this->postCommands[] = $this->createIndex($index);
        }
    }

    public function createIndex($index)
    {
        $type = $index->getType();
        return "CREATE $type `{$index->name}` ON `{$this->table->name}` (`{$index->field}`)";
    }

    public function dropField($name)
    {
        return "DROP COLUMN `$name`";
    }

    public function dropIndex($index)
    {
        return "DROP INDEX `{$index->name}` ON `{$this->table->name}`";
    }
}
