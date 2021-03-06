<?php

declare(strict_types=1);

namespace Nymfonya\Component\Http;

use Nymfonya\Component\Http\Interfaces\RoutesInterface;
use Nymfonya\Component\Http\Interfaces\RouteInterface;
use Nymfonya\Component\Http\Interfaces\RequestInterface;
use Nymfonya\Component\Http\Interfaces\RouterInterface;

class Router implements RouterInterface
{
    /**
     * active route
     *
     * @var string
     */
    private $activeRoute;

    /**
     * routes collection
     *
     * @var RouteInterface[]
     */
    private $routes;

    /**
     * request
     *
     * @var RequestInterface
     */
    private $request = null;

    /**
     * route params
     *
     * @var array
     */
    private $params;

    /**
     * route match expr
     *
     * @var string
     */
    private $matchingRoute;

    /**
     * instanciate
     *
     * @param RoutesInterface $routes
     * @param RequestInterface $request
     */
    public function __construct(RoutesInterface $routes, RequestInterface $request)
    {
        $this->routes = $routes->get();
        $this->request = $request;
        $this->activeRoute = '';
        $this->params = [];
        $this->matchingRoute = '';
        $this->activeRoute = (string) substr($this->request->getUri(), 1);
    }

    /**
     * set routes
     *
     * @param RoutesInterface $routes
     * @return RouterInterface
     */
    public function setRoutes(RoutesInterface $routes): RouterInterface
    {
        $this->routes = $routes->get();
        return $this;
    }

    /**
     * compile
     *
     * @return array
     */
    public function compile(): array
    {
        $routes = $this->routes;
        $routesLength = count($routes);
        for ($i = 0; $i < $routesLength; $i++) {
            $route = $routes[$i];
            $matches = [];
            $pattern = $route->getExpr();
            $match = preg_match($pattern, $this->activeRoute, $matches);
            if ($match) {
                $this->matchingRoute = $pattern;
                array_shift($matches);
                $this->setParams($route, $matches);
                return $matches;
            }
        }
        return [];
    }

    /**
     * return slugs params
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * set params from slugs
     *
     * @param RouteInterface $route
     * @param array $matches
     * @return RouterInterface
     */
    public function setParams(RouteInterface $route, array $matches): RouterInterface
    {
        $slugs = $route->getSlugs();
        $slugCount = count($slugs);
        if ($slugCount > 0) {
            for ($c = 0; $c < $slugCount; $c++) {
                $slug = $slugs[$c];
                if (false === empty($slug)) {
                    $this->params[$slug] = $matches[$c];
                }
            }
        } else {
            if (isset($matches[2])) {
                $parms = explode('/', $matches[2]);
                $this->params = [];
                while (false !== $key = next($parms)) {
                    $this->params[$key] = next($parms);
                }
            }
        }
        return $this;
    }

    /**
     * return matching regexp pattern
     *
     * @return string
     */
    public function getMatchingRoute(): string
    {
        return $this->matchingRoute;
    }
}
