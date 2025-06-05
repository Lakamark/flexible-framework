<?php

/**
 * The main configuration for your database.
 */

use Psr\Container\ContainerInterface;

return [
    'database.host' => getenv('DATABASE_HOST') ?: 'localhost',
    'database.username' => getenv('DATABASE_USERNAME'),
    'database.password' => getenv('DATABASE_PASSWORD'),
    'database.name' => getenv('DATABASE_NAME'),
    \PDO::class => function (ContainerInterface $c) {
        return new PDO(
            'mysql:host=' . $c->get('database.host') . ';dbname=' . $c->get('database.name'),
            $c->get('database.username'),
            $c->get('database.password'),
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
            ]
        );
    },
];
