<?php

namespace Nymfonya\Component\Http;

use Nymfonya\Component\Http\Interfaces\CookieInterface;

class Cookie implements CookieInterface
{

    protected $cookie;

    /**
     * instanciate
     *
     * @return Cookie
     */
    public function __construct()
    {
        $this->refreshCookie();
    }

    /**
     * get cookie value from cookie name
     *
     * @param string $name
     * @return string
     */
    public function getCookie(string $name): string
    {
        return (isset($this->cookie[$name])) ? $this->cookie[$name] : '';
    }

    /**
     * set cookie value for cookie name and ttl
     *
     * @param string $name
     * @param string $value
     * @param integer $ttl
     * @return CookieInterface
     */
    public function setCookie(string $name, string $value, int $ttl): CookieInterface
    {
        setcookie($name, $value, time() + $ttl);
        return $this->refreshCookie();
    }

    /**
     * refresh cookie from global
     *
     * @return CookieInterface
     */
    protected function refreshCookie(): CookieInterface
    {
        $this->cookie = $_COOKIE;
        return $this;
    }
}
