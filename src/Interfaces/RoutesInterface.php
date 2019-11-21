<?php

namespace Nymfonya\Component\Http\Interfaces;

interface RoutesInterface
{
    /**
     * instanciate
     *
     * @param array $routes
     * @return void
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
