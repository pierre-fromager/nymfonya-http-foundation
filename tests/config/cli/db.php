<?php

// Compatibility alias for PHP 8.5+ (Pdo\Mysql\ATTR_INIT_COMMAND doesn't exist yet)
if (!defined('Pdo\Mysql\ATTR_INIT_COMMAND')) {
    @define('Pdo\Mysql\ATTR_INIT_COMMAND', \PDO::MYSQL_ATTR_INIT_COMMAND);
}

/**
 * slot => [ dbname => [...params]]
 */

return [
    'test' => [
        'nymfonya' => [
            'host' => 'localhost',
            'username' => 'travis_pass',
            'password' => 'some password',
            'adapter' => App\Component\Db\Adapter\PdoMysql::class,
            'options' => [
                Pdo\Mysql\ATTR_INIT_COMMAND => 'SET NAMES utf8',
                \PDO::ATTR_PERSISTENT => false,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                //, \PDO::ERRMODE_EXCEPTION => false
                \PDO::ATTR_CASE => \PDO::CASE_LOWER,
                \PDO::ATTR_EMULATE_PREPARES => false
            ]
        ],
        'badnymfonya' => [
            'host' => 'localhost',
            'username' => 'travis_pass',
            'password' => 'wrong password',
            'adapter' => App\Component\Db\Adapter\PdoMysql::class,
            'options' => [
                Pdo\Mysql\ATTR_INIT_COMMAND => 'SET NAMES utf8',
                \PDO::ATTR_PERSISTENT => false,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                //, \PDO::ERRMODE_EXCEPTION => false
                \PDO::ATTR_CASE => \PDO::CASE_LOWER,
                \PDO::ATTR_EMULATE_PREPARES => false
            ]
        ]
    ]
];
