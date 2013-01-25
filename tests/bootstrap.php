<?php

error_reporting(E_ALL | E_STRICT);

require_once 'PHPUnit/TextUI/TestRunner.php';

// Include the composer autoloader
$autoloader = require dirname(__DIR__) . '/vendor/autoload.php';

$pdo = new \PDO("mysql:host=localhost;dbname=scheezy", 'scheezy', '');
$result = $pdo->query('show tables');
$tables = $result->fetchAll(\PDO::FETCH_COLUMN, 0);
array_map(
    function ($table) use ($pdo) {
        $pdo->exec("DROP TABLE `$table`");
    },
    $tables
);
unset($pdo, $result, $tables);
