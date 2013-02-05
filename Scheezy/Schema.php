<?php

namespace Scheezy;

use Symfony\Component\Yaml\Yaml;

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
        $yaml = Yaml::parse($str);
        $database = new Database($yaml, $this->connection);
        $this->databases[] = $database;
        return $database;
    }

    public function loadFile($file)
    {
        $yaml = Yaml::parse($file);
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

    public function __toString()
    {
        $sql = '';
        foreach ($this->databases as $database) {
            $sql .= $database->__toString();
        }
        return $sql;
    }
}
