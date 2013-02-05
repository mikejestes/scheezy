<?php

namespace Scheezy\Table\Creator;

class Sqlite extends Mysql
{
    public function createIndex($index)
    {
        $type = $index->getType();
        return "CREATE $type `{$index->name}` ON `{$this->table->name}` (`{$index->field}`)";
    }

    public function combineCommands()
    {
        $this->removeEmpty();
        return implode(";\n", array_merge(array(implode(",\n", $this->mainCommands) . "\n)"), $this->postCommands));
    }
}
