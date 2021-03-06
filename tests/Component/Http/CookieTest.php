<?php

namespace Nymfonya\Component\HttpFoundation\Tests\Component\Http;

use PHPUnit\Framework\TestCase as PFT;
use Nymfonya\Component\Http\Cookie;

/**
 * @covers \Nymfonya\Component\Http\Cookie::<public>
 */
class CookieTest extends PFT
{

    const TEST_ENABLE = true;

    /**
     * instance
     *
     * @var Cookie
     */
    protected $instance;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        if (!self::TEST_ENABLE) {
            $this->markTestSkipped('Test disabled.');
        }
        $this->instance = new Cookie();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        $this->instance = null;
    }

    /**
     * get any method from a class to be invoked whatever the scope
     *
     * @param String $name
     * @return void
     */
    protected static function getMethod(string $name)
    {
        $class = new \ReflectionClass(Cookie::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers Nymfonya\Component\Http\Cookie::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Cookie);
    }

    /**
     * testGetCookie
     * @covers Nymfonya\Component\Http\Cookie::getCookie
     */
    public function testGetCookie()
    {
        $this->assertTrue(is_string($this->instance->getCookie('')));
    }

    /**
     * testSetCookie
     * @covers Nymfonya\Component\Http\Cookie::setCookie
     * @runInSeparateProcess
     */
    public function testSetCookie()
    {
        $expected = $this->instance->setCookie(
            'cookie',
            'cookieValue',
            0
        ) instanceof Cookie;
        $this->assertTrue($expected);
    }

    /**
     * testRefreshCookie
     * @covers Nymfonya\Component\Http\Cookie::refreshCookie
     * @runInSeparateProcess
     */
    public function testRefreshCookie()
    {
        self::getMethod('refreshCookie')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(
            $this->instance instanceof Cookie
        );
    }
}
