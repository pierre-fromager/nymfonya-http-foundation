<?php

namespace Nymfonya\Component\Http\Interfaces;

use Nymfonya\Component\Http\Interfaces\RequestInterface;
use Nymfonya\Component\Http\Interfaces\RoutesInterface;
use Nymfonya\Component\Http\Interfaces\RouteInterface;

interface RouterInterface
{
    const URI_SEPARATOR = '/';
    const REQUEST_URI = 'REQUEST_URI';

    /**
     * instanciate
     *
     * @param RoutesInterface $routes
     * @param RequestInterface $request
     * @return RouterInterface
     */
    public function __construct(RoutesInterface $routes, RequestInterface $request);

    /**
     * assign routes to router
     *
     * @param RoutesInterface $routes
     * @return RouterInterface
     */
    public function setRoutes(RoutesInterface $routes): RouterInterface;

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
     * @return RouRouterInterfaceter
     */
    public function setParams(RouteInterface $route, array $matches): RouterInterface;

    /**
     * return matching regexp pattern
     *
     * @return string
     */
    public function getMatchingRoute(): string;
}
