<?php

declare(strict_types=1);

namespace Nymfonya\Component\Http;

use Nymfonya\Component\Http\Interfaces\RoutesInterface;
use Nymfonya\Component\Http\Route;

class Routes implements RoutesInterface
{

    /**
     * route list as array
     *
     * @var array
     */
    private $routes = [];

    /**
     * __construct
     *
     * @param array $routesConfig
     */
    public function __construct(array $routesConfig = [])
    {
        if (!empty($routesConfig)) {
            $this->set($routesConfig);
        }
    }

    /**
     * set routes as array and stack Route collection
     *
     * @param array $routesConfig
     * @return RoutesInterface
     */
    public function set(array $routesConfig): RoutesInterface
    {
        $this->routes = [];
        $this->prepare($routesConfig);
        $this->validate();
        return $this;
    }

    /**
     * returns routes as array
     *
     * @return array
     */
    public function get(): array
    {
        return $this->routes;
    }

    /**
     * returns routes as array
     *
     * @return array
     */
    public function getExpr(): array
    {
        $patterns = array_map(
            function (Route $i) {
                return $i->getExpr();
            },
            $this->routes
        );
        return $patterns;
    }

    /**
     * stacks routes as Route object collection from routes config
     *
     * @param array $routesConfig
     * @return RoutesInterface
     */
    protected function prepare(array $routesConfig): RoutesInterface
    {
        $count = count($routesConfig);
        for ($c = 0; $c < $count; $c++) {
            $this->routes[] = new Route($routesConfig[$c]);
        }
        return $this;
    }

    /**
     * validate routes to be an array of regexp string
     *
     * @throws Exception
     */
    protected function validate()
    {
        $count = count($this->routes);
        for ($c = 0; $c < $count; $c++) {
            $route = $this->routes[$c]->getExpr();
            if ($this->isInvalidRegexp($route)) {
                throw new \Exception('Route invalid expr ' . $route);
            }
        }
    }

     /**
      * return true if regexp is invalid
      *
      * @param string $regExp
      * @return boolean
      */
    protected function isInvalidRegexp(string $regExp): bool
    {
        @preg_match($regExp, '');
        return (preg_last_error() != PREG_NO_ERROR);
    }
}
