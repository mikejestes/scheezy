<?php

namespace Scheezy\Table\Modifier;

class Mysql extends \Scheezy\Table\Creator\Mysql
{
    protected $yaml;
    protected $table;
    protected $indexes = array();

    public function __construct(\Scheezy\Table $table, $yaml)
    {
        $this->table = $table;
        $this->yaml = $yaml;
    }

    public function toString()
    {
        $sql = "ALTER TABLE `{$this->table->name}` (\n";

        foreach ($this->table->columns() as $column) {
            if (!in_array($column->Field, array_keys($this->yaml['columns']))) {
                $sql .= $this->dropField($column->Field);
                $sql .= ",\n";
            }
        }

        foreach ($this->yaml['columns'] as $fieldName => $fieldOptions) {
            $line = $this->insureField($fieldName, $fieldOptions);
            if ($line) {
                $sql .= "$line,\n";
            }
        }

        foreach ($this->indexes as $indexOptions) {
            $sql .= $this->createIndex($indexOptions);
            $sql .= ",\n";
        }

        $sql = rtrim($sql, ",\n");

        $sql .= "\n)";

        return $sql;
    }

    public function insureField($name, $options)
    {
        if ($this->table->columnExists($name)) {
            return $this->modifyField($name, $options);
        } else {
            return 'ADD COLUMN ' . $this->createField($name, $options);
        }
    }

    public function modifyField($name, $options)
    {
        $currentColumnDetails = $this->table->columnDetail($name);
        $newLine = $this->createField($name, $options, false);
        $currentLine = "`$name` {$currentColumnDetails->Type}";
        if ($currentColumnDetails->Null == 'NO') {
            $currentLine .= " NOT NULL";
        }
        if ($currentColumnDetails->Extra == 'auto_increment') {
            $currentLine .= " AUTO_INCREMENT";
        }
        if ($newLine != $currentLine) {
            return 'CHANGE ' . $newLine;
        }
    }

    public function dropField($name)
    {
        return "DROP COLUMN `$name`";
    }
}
