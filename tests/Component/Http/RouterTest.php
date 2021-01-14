<?php

namespace Nymfonya\Component\HttpFoundation\Tests\Component\Http;

use PHPUnit\Framework\TestCase as PFT;
use Nymfonya\Component\Http\Request;
use Nymfonya\Component\Http\Route;
use Nymfonya\Component\Http\Routes;
use Nymfonya\Component\Http\Router;

/**
 * @covers \Nymfonya\Component\Http\Router::<public>
 */
class RouterTest extends PFT
{

    const TEST_ENABLE = true;
    const MATCH_ALL = ['/.*$/'];

    /**
     * instance
     *
     * @var RouterTest
     */
    protected $instance;

    /**
     * routes from config routes
     *
     * @return array
     */
    protected function routesConfig(): array
    {
        return [
            '/^(api\/v1\/auth)\/(.*)$/',
            '/^(config)\/(help)$/',
            '/^(config)\/(keygen)$/',
        ];
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        if (!self::TEST_ENABLE) {
            $this->markTestSkipped('Test disabled.');
        }
        $this->instance = new Router(
            new Routes($this->routesConfig()),
            new Request()
        );
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
     * testInstance
     * @covers Nymfonya\Component\Http\Router::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Router);
    }

    /**
     * testSetRoutes
     * @covers Nymfonya\Component\Http\Router::setRoutes
     */
    public function testSetRoutes()
    {
        $this->assertTrue(
            $this->instance->setRoutes(
                new Routes(self::MATCH_ALL)
            ) instanceof Router
        );
    }

    /**
     * testGetSetParams
     * @covers Nymfonya\Component\Http\Router::getParams
     * @covers Nymfonya\Component\Http\Router::setParams
     */
    public function testGetSetParams()
    {
        $params0 = $this->instance->getParams();
        $this->assertTrue(is_array($params0));
        $this->assertEmpty($params0);
        $routeSlugged = new Route(
            'GET;/^(api\/v1\/restful)\/(\d+)$/;,id'
        );
        $matches = [
            '',
            100
        ];
        $rsp = $this->instance->setParams($routeSlugged, $matches);
        $this->assertTrue($rsp instanceof Router);
        $params1 = $this->instance->getParams();
        $this->assertEquals(['id' => 100], $params1);
    }

    /**
     * testGetMatchingRoute
     * @covers Nymfonya\Component\Http\Router::getMatchingRoute
     */
    public function testGetMatchingRoute()
    {
        $mar = $this->instance->getMatchingRoute();
        $this->assertTrue(is_string($mar));
        $this->assertEmpty($mar);
    }

    /**
     * testCompile
     * @covers Nymfonya\Component\Http\Router::compile
     */
    public function testCompile()
    {
        $comp0 = $this->instance->compile();
        $this->assertTrue(is_array($comp0));
        $this->assertEmpty($comp0);
        $this->instance->setRoutes(new Routes(self::MATCH_ALL));
        $comp1 = $this->instance->compile();
        $this->assertTrue(is_array($comp1));
    }

    /**
     * testGetParams
     * @covers Nymfonya\Component\Http\Router::compile
     * @covers Nymfonya\Component\Http\Router::getParams
     * @dataProvider routesGetParamsDataProvider
     */
    public function testGetParams($uri, $rex, $expectedParams)
    {
        $mockRequest = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $mockRequest->method('getUri')->willReturn($uri);
        $routes = new Routes([$rex]);
        $router = new Router($routes, $mockRequest);
        $router->compile();
        $this->assertEquals($router->getParams(), $expectedParams);
    }

    /**
     * routesGetParamsDataProvider
     * @return Array
     */
    public function routesGetParamsDataProvider()
    {
        $uri = '/api/v1/';
        return [

            'matchMultipleParams' => [
                $uri . 'auth/login/username/test/password/pwd',
                '/^(api\/v1\/auth)\/(.*?)(\/.*)/',
                ['username' => 'test', 'password' => 'pwd']
            ],
            'notRouterJobButRequestShouldGetParams' => [
                $uri . 'auth/info?id=1',
                '/^(api\/v1\/auth)\/(.*?)(\?.*)/',
                []
            ],
            'matchClassic' => [
                $uri . 'auth/info/id/1',
                '/^(api\/v1\/auth)\/(.*?)(\/.*)/',
                ['id' => 1]
            ],
            'matchSlug' => [
                $uri . 'auth/info/10',
                'GET;/^(api\/v1\/auth\/info)\/(\d+)$/;,id',
                ['id' => 10]
            ],
        ];
    }
}
