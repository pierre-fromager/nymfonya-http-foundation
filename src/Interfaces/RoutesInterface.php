<?php

namespace Nymfonya\Component\Http\Interfaces;

use Nymfonya\Component\Http\Routes;

interface RoutesInterface
{
    /**
     * instanciate
     *
     * @param array $routes
     * @return RoutesInterface
     */
    public function __construct(array $routes);

    /**
     * get routes
     *
     * @return array
     */
    public function get(): array;

    /**
     * set routes from array
     *
     * @param array $routes
     * @return RoutesInterface
     */
    public function set(array $routes): RoutesInterface;
}
