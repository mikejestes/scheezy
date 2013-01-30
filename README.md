Scheezy
=======

A PHP 5.3+ library to translate yaml schema definitions into real actual databases.
You could view this as an intentional anti-pattern to database migration techniques.
For when you don't want to migrate up and down, just dictate the database schema.

[![Build Status](https://travis-ci.org/mikejestes/scheezy.png?branch=master)](https://travis-ci.org/mikejestes/scheezy)

## Installation

Add to your `composer.json` file:

```yaml
    {
        "require": {
            "mikejestes/scheezy": "*"
        },
        
        ...
    }
```

Then download and run [composer](http://getcomposer.org/):

    curl -s https://getcomposer.org/installer | php
    php composer.phar install

## Schema Definition Syntax

```yaml
table: store
columns:
    id:
    name:
        type: string
        length: 80
    email:
        index: unique
    active:
        type: boolean
    user_count:
        type: integer
        index: true
    website:
    created:
        type: datetime
    updated:
        type: timestamp
    calendar:
        type: date
    paragraph:
        type: text
    price:
        type: decimal
    latitude:
        type: decimal
        precision: 9
        scale: 6

```

A column defaults to string type, unless the name is `id` which is given an integer type, a primary key, and auto_increment.

## Database engines

Currently supports mysql and sqlite through PDO classes. Sqlite support for changing or dropping columns is pending.

## API

Load a directory of `.yaml` files

```php
$pdoHandle = new PDO('sqlite::memory:');

$schema = new \Scheezy\Schema($pdoHandle);
$schema->loadDirectory(dirname(__FILE__) . '/schemas/');
$schema->synchronize();
```

Load a single `.yaml` file

```php
$schema = new \Scheezy\Schema($pdoHandle);
$schema->loadFile('/path/to/ponys.yaml');
$schema->synchronize();
```

## Alternatives

For actual migration style (up, down, rollback) try one of these:
* https://github.com/doctrine/migrations
* https://github.com/davedevelopment/phpmig
