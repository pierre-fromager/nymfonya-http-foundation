<?php

namespace Nymfonya\Component\Http\Interfaces;

use \Closure;
use Nymfonya\Component\Container;

interface MiddlewareInterface
{
    const _EXCLUDE = 'exclude';
    const _INCLUDE = 'include';
    const _PREFIX = 'prefix';

    /**
     * peel
     *
     * @param Container $container
     * @param Closure $next
     * @return void
     */
    public function peel(Container $container, Closure $next);
}
