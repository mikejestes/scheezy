Scheezy
=======

A PHP 5.3+ library to translate yaml schema definitions into real actual databases. 
You could view this as an intentional anti-pattern to database migration techniques. 
For when you don't want to migrate up and down, just dictate the database schema.

## Syntax

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
        phone:
```

A column defaults to string type, unless the name is `id` which is given an integer type, a primary key, and auto_increment.

## Database engines

Currently supports mysql and sqlite through PDO classes.
