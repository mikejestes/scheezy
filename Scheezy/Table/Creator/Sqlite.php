<?php

namespace Scheezy\Table\Creator;

class Sqlite extends Mysql
{
    public function createIndex($options)
    {
        return $this->table->createIndex($options);
    }

    public function combineCommands($commands, $postCommands)
    {
        return implode(";\n", array_merge(array(implode(",\n", $commands) . "\n)"), $postCommands));
    }
}
