<?php

namespace Nymfonya\Component\HttpFoundation\Tests\Middlewares;

use Nymfonya\Component\Http\Interfaces\Middleware\ILayer;
use Nymfonya\Component\Http\Response;
use Nymfonya\Component\Container;
use Closure;

class After implements ILayer
{

    const _SIGN = 'X-Middleware-After';

    /**
     * peel poil
     *
     * @param Container $container
     * @param Closure $next
     * @return Container
     */
    public function peel(Container $container, Closure $next): Container
    {
        $res = $next($container);
        $response = $container->getService(Response::class);
        $response->getHeaderManager()->add(
            self::_SIGN,
            microtime(true)
        );
        return $res;
    }
}
