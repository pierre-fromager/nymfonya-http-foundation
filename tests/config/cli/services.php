<?php

use Nymfonya\Component\Config;
use Nymfonya\Component\Http\Request;
use Nymfonya\Component\Http\Response;
use Nymfonya\Component\Http\Routes;
use Nymfonya\Component\Http\Router;

return [
    Config::class => [Config::ENV_CLI, __DIR__ . '/../'],
    Request::class => [],
    Response::class => [],
    Routes::class => [include(__DIR__ . '/routes.php')],
    Router::class => [
        Routes::class,
        Request::class
    ],
    \Monolog\Handler\RotatingFileHandler::class => [
        realpath(__DIR__ . '/../../logs') . '/console.txt',
        0,
        \Monolog\Logger::DEBUG,
        true,
        0777
    ],
    \Monolog\Logger::class => [
        Config::_NAME,
        [\Monolog\Handler\RotatingFileHandler::class],
        [\Monolog\Processor\WebProcessor::class]
    ],
];
