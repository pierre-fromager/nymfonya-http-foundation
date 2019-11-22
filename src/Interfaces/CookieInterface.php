<?php

namespace Nymfonya\Component\Http\Interfaces;

interface CookieInterface
{

    /**
     * get cookie string
     *
     * @param string $name
     * @return string
     */
    public function getCookie(string $name): string;

    /**
     * set cookie value for cookie name and ttl
     *
     * @param string $name
     * @param string $value
     * @param integer $ttl
     * @return CookieInterface
     */
    public function setCookie(string $name, string $value, int $ttl): CookieInterface;
}
