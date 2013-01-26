<?php

namespace Scheezy;

class Schema
{
    private $connection;
    private $databases = array();

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function loadString($str)
    {
        $yaml = spyc_load($str);
        $database = new Database($yaml, $this->connection);
        $this->databases[] = $database;
        return $database;
    }

    public function loadFile($file)
    {
        $yaml = spyc_load_file($file);
        $database = new Database($yaml, $this->connection);
        $this->databases[] = $database;
        return $database;
    }

    public function loadDirectory($directory)
    {
        $files = glob($directory . '/*.yaml');
        foreach ($files as $file) {
            $this->loadFile($file);
        }
    }

    public function loadGlob($files)
    {
        foreach ($files as $file) {
            $this->loadFile($file);
        }
    }

    public function synchronize()
    {
        foreach ($this->databases as $database) {
            $database->synchronize();
        }
    }

    public function toString()
    {
        $sql = '';
        foreach ($this->databases as $database) {
            $sql .= $database->toString();
        }
        return $sql;
    }
}
