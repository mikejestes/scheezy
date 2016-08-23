<?php

namespace Scheezy\Column;

class Definition
{
    private $driver;
    private $basicTypes = array(
        'boolean' => 'tinyint(1)',
        'datetime' => 'datetime',
        'date' => 'date',
        'time' => 'time',
        'year' => 'year',
        'timestamp' => 'timestamp',
        'text' => 'text',
    );

    public function __construct($driver)
    {
        $this->driver = $driver;
    }

    public function __call($desiredFnc, $args)
    {
        $fnc = str_replace('make', 'create', $desiredFnc);

        $type = strtolower(str_replace('make', '', $desiredFnc));

        if (array_key_exists($type, $this->basicTypes)) {
            return $this->createBasic($args[0], $type, $args[1]);
        }

        if (!method_exists($this, $fnc)) {
            throw new \Exception('Unknown Scheezy type: ' . $type);
        }

        return call_user_func_array(array($this, $fnc), $args);
    }

    public function createString($name, $options)
    {
        $length = $this->getOption($options, 'length', 255);
        $null = empty($options['allow_null']) ? ' NOT NULL' : '';
        return "`$name` varchar($length){$null}";
    }

    public function createInteger($name, $options)
    {
        $length = $this->getOption($options, 'length', 11);
        $autoIncrement = $this->getOption($options, 'auto_increment');
        $primaryKey = $this->getOption($options, 'primary_key');
        $default = $this->getOption($options, 'default');
        $extra = $autoIncrement ? ' AUTO_INCREMENT' : '';
        $extra .= $primaryKey ? ' PRIMARY KEY' : '';
        $extra .= $default !== null ? (' DEFAULT ' . $default) : '';
        $null = empty($options['allow_null']) ? ' NOT NULL' : '';
        return "`$name` int($length){$null}{$extra}";
    }

    public function createBasic($name, $type, $options)
    {
        $typeDef = $this->basicTypes[$type];
        $null = empty($options['allow_null']) ? ' NOT NULL' : '';
        return "`$name` $typeDef{$null}";
    }

    public function createDecimal($name, $options)
    {
        $precision = $this->getOption($options, 'precision', 10);
        $scale = $this->getOption($options, 'scale', 2);
        $null = empty($options['allow_null']) ? ' NOT NULL' : '';
        return "`$name` decimal($precision,$scale){$null}";
    }

    public function createEnum($name, $options)
    {
        $values = $this->getOption($options, 'values');
        if (!$values) {
            throw new \Exception('ENUM types must specify a set of values.');
        }
        $joinedValues = implode("','", $values);
        return "`$name` enum('$joinedValues')" ;
    }

    public function getOption($options, $key, $default = null)
    {
        if (!isset($options[$key])) {
            return $default;
        }

        return $options[$key];
    }

    public function createId($name, $options)
    {

        $options = array_merge(
            $options,
            array(
                'auto_increment' => true,
                'primary_key' => true,
            )
        );

        return $this->makeInteger($name, $options);
    }
}
