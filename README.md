Laravel TDBM bindings
=====================

TDBM integration package for Laravel

## Overview

`thecodingmachine/tdbm-laravel` package provides:

- **TDBMService** setup with the appropriate configuration based on your default Laravel DB connection, initialized by same PDO connection object
- An **artisan command** for generating PHP code (DAOs and beans)

This package requires a Doctrine DBAL connection that is provided by the `nayjest/laravel-doctrine-dbal` package.

## Installation

Via [Composer](https://getcomposer.org)

* Run following command:
```bash
composer require thecodingmachine/tdbm-laravel
```

* Register `Nayjest\LaravelDoctrineDBAL\ServiceProvider` in your application configuration file (`config/app.php`)
* Register `TheCodingMachine\TDBM\Laravel\TDBMServiceProvider` in your application configuration file (`config/app.php`)

```php
    'DBAL' => 'Nayjest\LaravelDoctrineDBAL\Facade',
```

## Generating beans and DAOs

Run the following command:

```bash
php artisan tdbm:generate
```

You must run this command after the installation of the package, and **each time you run a migration** (i.e. each time the database model changes).

## Advanced configuration

By default, TDBM will write DAOs in the `App\Daos` namespace and beans in the `App\Beans` namespace.
If you want to customize this, you can edit the `config/database.php` file:

```php
<?php

return [

    // ...

    /*
    |--------------------------------------------------------------------------
    | TDBM Configuration
    |--------------------------------------------------------------------------
    |
    | Use this configuration to customize the namespace of DAOs and beans.
    | These namespaces must be autoloadable from Composer.
    | TDBM will find the path of the files based on Composer.
    |
    */


    'tdbm' => [
        'daoNamespace' => 'App\\Daos',
        'beanNamespace' => 'App\\Beans',
    ]
];
```
