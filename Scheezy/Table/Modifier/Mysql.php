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
        $modifications = array();
        $postCommands = array();

        // see if columns need to be dropped
        foreach ($this->table->columns() as $column) {
            if (!in_array($column->Field, array_keys($this->yaml['columns']))) {
                $modifications[] = $this->dropField($column->Field);
            }
        }

        // see if columns need to be modified or added
        foreach ($this->yaml['columns'] as $fieldName => $fieldOptions) {
            $modifications[] = $this->insureField($fieldName, $fieldOptions);
        }

        // see what indexes need to be added
        $desiredIndexes = array();
        foreach ($this->table->indexes as $indexOptions) {
            $desiredIndexes[] = $indexOptions['name'];
            $postCommands[] = $this->insureIndex($indexOptions);
        }

        // see what indexes need to be dropped
        $columnKey = 'Column_name';
        $nameKey = 'Key_name';

        // HACK
        if ($this->table instanceof \Scheezy\Table\Sqlite) {
            $columnKey = $nameKey = 'name';
        }

        foreach ($this->table->indexes() as $index) {
            if (!in_array($index[$columnKey], $desiredIndexes) && $index[$nameKey] != 'PRIMARY') {
                $postCommands[] = $this->dropIndex($index[$nameKey]);
            }
        }

        $modifications = array_filter(
            $modifications,
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

        return $this->combineCommands($modifications, $postCommands);
    }

    public function combineCommands($modifications, $postCommands)
    {
        $commands = array();

        if (count($modifications)) {
            $action = implode(",\n", $modifications);
            if (count($modifications) > 0) {
                $action = "\n$action";
            }
            $commands[] = "ALTER TABLE `{$this->table->name}`$action";
        }

        $sql = implode(";\n", array_merge($commands, $postCommands));

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

    public function insureIndex($options)
    {
        if ($this->table->indexExists($options['name'])) {
            // return $this->modifyField($name, $options);
        } else {
            return $this->table->createIndex($options);
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

    public function dropField($name)
    {
        return "DROP COLUMN `$name`";
    }

    public function dropIndex($name)
    {
        return "DROP INDEX `$name` ON `{$this->table->name}`";
    }
}
