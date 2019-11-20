<?php

use Tests\Middlewares\After;

return [
    After::class => [
        'enabled' => true,
        'prefix' => '/api/v1/',
        'exclude' => ['auth/login'],
    ],
];
