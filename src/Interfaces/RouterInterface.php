<?php

namespace Nymfonya\Component\Http\Interfaces;

use Nymfonya\Component\Http\Interfaces\RequestInterface;
use Nymfonya\Component\Http\Interfaces\RoutesInterface;
use Nymfonya\Component\Http\Interfaces\RouteInterface;
use Nymfonya\Component\Http\Router;

interface RouterInterface
{
    const URI_SEPARATOR = '/';
    const REQUEST_URI = 'REQUEST_URI';

    /**
     * instanciate
     *
     * @param RoutesInterface $routes
     * @param RequestInterface $request
     */
    public function __construct(RoutesInterface $routes, RequestInterface $request);

    /**
     * assign routes to router
     *
     * @param RoutesInterface $routes
     * @return Router
     */
    public function setRoutes(RoutesInterface $routes): Router;

    /**
     * compiles routes
     *
     * @return array
     */
    public function compile(): array;

    /**
     * return slugs params
     *
     * @return array
     */
    public function getParams(): array;

    /**
     * set params from slugs
     *
     * @param RouteInterface $route
     * @param array $matches
     * @return Router
     */
    public function setParams(RouteInterface $route, array $matches): Router;

    /**
     * return matching regexp pattern
     *
     * @return string
     */
    public function getMatchingRoute(): string;
}
