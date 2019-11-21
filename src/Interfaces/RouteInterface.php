<?php

namespace Nymfonya\Component\Http\Interfaces;

interface RouteInterface
{
    /**
     * instanciate
     *
     * @param string $routeItem
     */
    public function __construct(string $routeItem);

    /**
     * return regexp pattern
     *
     * @return string
     */
    public function getExpr(): string;

    /**
     * return required request method
     *
     * @return string
     */
    public function getMethod(): string;

    /**
     * return slugs
     *
     * @return string
     */
    public function getSlugs(): array;
}
