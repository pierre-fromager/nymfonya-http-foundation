<?php

namespace Nymfonya\Component\HttpFoundation\Tests\Component\Http;

use PHPUnit\Framework\TestCase as PFT;
use Nymfonya\Component\Config;
use Nymfonya\Component\Container;
use Nymfonya\Component\Http\Request;
use Nymfonya\Component\Http\Response;
use Nymfonya\Component\Http\Router;
use Nymfonya\Component\Http\Kernel;
use Nymfonya\Component\Http\Interfaces\KernelInterface;
use Nymfonya\Component\Pubsub\Dispatcher;

/**
 * @covers \Nymfonya\Component\Http\Kernel::<public>::<public>
 */
class KernelTest extends PFT
{

    const TEST_ENABLE = true;
    const CONFIG_PATH = '/../../../tests/config/';
    const KERNEL_PATH =  '/../';
    const KERNEL_NS = 'Nymfonya\\Component\\HttpFoundation\\Tests\\Controllers\\';
    const CTRL_ACT = ['config', 'help'];
    const CTRL_ACTIONS = [
        'swaggerdoc', 'false', 'preflight', 'help', 'account', 'keygen'
    ];

    /**
     * config
     *
     * @var Config
     */
    protected $config;

    /**
     * instance
     *
     * @var Kernel
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
        $this->instance = new Kernel(
            Config::ENV_CLI,
            __DIR__ . self::KERNEL_PATH
        );
        $this->instance->setNameSpace(self::KERNEL_NS);
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
        $class = new \ReflectionClass(Kernel::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers Nymfonya\Component\Http\Kernel::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Kernel);
    }

    /**
     * testRunOk
     * @covers Nymfonya\Component\Http\Kernel::run
     */
    public function testRunOk()
    {
        $routerGroups = ['config', 'help'];
        $kr = $this->instance->run($routerGroups);
        $this->assertTrue($kr instanceof Kernel);
        $res = $kr->getService(Response::class);
        $this->assertTrue($res instanceof Response);
        $this->assertEquals($res->getCode(), Response::HTTP_OK);
    }

    /**
     * testRunNok
     * @covers Nymfonya\Component\Http\Kernel::run
     */
    public function testRunNok()
    {
        $routerGroups = ['badctrl', 'messup'];
        $kr = $this->instance->run($routerGroups);
        $this->assertTrue($kr instanceof Kernel);
        $res = $kr->getService(Response::class);
        $this->assertTrue($res instanceof Response);
        $this->assertEquals($res->getCode(), Response::HTTP_NOT_FOUND);
    }

    /**
     * testSend
     * @covers Nymfonya\Component\Http\Kernel::send
     * @covers Nymfonya\Component\Http\Kernel::setError
     * @covers Nymfonya\Component\Http\Kernel::getError
     * @runInSeparateProcess
     */
    public function testSend()
    {
        $this->setOutputCallback(function () {
        });
        $kr = $this->instance->run();
        $this->assertTrue($kr instanceof Kernel);
        $ks = $kr->send();
        $this->assertTrue($ks instanceof Kernel);
        self::getMethod('setError')->invokeArgs(
            $this->instance,
            [false]
        );
        $ge = self::getMethod('getError')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertFalse($ge);
        $kse = $this->instance->send();
        $this->assertTrue($kse instanceof Kernel);
    }

    /**
     * testSetNameSpace
     * @covers Nymfonya\Component\Http\Kernel::setNameSpace
     */
    public function testSetNameSpace()
    {
        $sns = self::getMethod('setNameSpace')->invokeArgs(
            $this->instance,
            [self::KERNEL_NS]
        );
        $this->assertTrue($sns instanceof Kernel);
    }

    /**
     * testInit
     * @covers Nymfonya\Component\Http\Kernel::init
     * @covers Nymfonya\Component\Http\Kernel::getContainer
     */
    public function testInit()
    {
        self::getMethod('init')->invokeArgs(
            $this->instance,
            [
                Config::ENV_CLI,
                __DIR__ . self::KERNEL_PATH
            ]
        );
        $this->assertTrue(
            $this->instance->getService(Request::class)
                instanceof Request
        );
        $this->assertTrue(
            $this->instance->getService(Response::class)
                instanceof Response
        );
        $this->assertTrue(
            $this->instance->getService(\Monolog\Logger::class)
                instanceof \Monolog\Logger
        );
        $gc = self::getMethod('getContainer')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($gc instanceof Container);
    }

    /**
     * testSetDispatcher
     * @covers Nymfonya\Component\Http\Kernel::setDispatcher
     */
    public function testSetDispatcher()
    {
        $sdi0 = $this->instance->setDispatcher();
        $this->assertTrue($sdi0 instanceof KernelInterface);
        $sdi1 = $this->instance->setDispatcher(new Dispatcher());
        $this->assertTrue($sdi1 instanceof KernelInterface);
    }

    /**
     * testGetBundleClassname
     * @covers Nymfonya\Component\Http\Kernel::getBundleClassname
     */
    public function testGetBundleClassname()
    {
        $gbc = $this->instance->getBundleClassname();
        $this->assertTrue(is_string($gbc));
        $this->assertNotEmpty($gbc);
    }

    /**
     * testSetGetContainer
     * @covers Nymfonya\Component\Http\Kernel::setContainer
     * @covers Nymfonya\Component\Http\Kernel::getContainer
     */
    public function testSetGetContainer()
    {
        self::getMethod('setContainer')->invokeArgs(
            $this->instance,
            []
        );
        $gc = self::getMethod('getContainer')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($gc instanceof Container);
    }

    /**
     * testSetGetError
     * @covers Nymfonya\Component\Http\Kernel::setError
     * @covers Nymfonya\Component\Http\Kernel::getError
     */
    public function testSetGetError()
    {
        $ges = self::getMethod('getError')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($ges);
        self::getMethod('setError')->invokeArgs(
            $this->instance,
            [false]
        );
        $ge = self::getMethod('getError')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertFalse($ge);
    }

    /**
     * testSetGetRequest
     * @covers Nymfonya\Component\Http\Kernel::setRequest
     * @covers Nymfonya\Component\Http\Kernel::getRequest
     */
    public function testSetGetRequest()
    {
        self::getMethod('setRequest')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(
            $this->instance->getService(Request::class)
                instanceof Request
        );
        $gr = self::getMethod('getRequest')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($gr instanceof Request);
    }

    /**
     * testSetGetResponse
     * @covers Nymfonya\Component\Http\Kernel::setResponse
     * @covers Nymfonya\Component\Http\Kernel::getResponse
     */
    public function testSetGetResponse()
    {
        self::getMethod('setResponse')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(
            $this->instance->getService(Response::class)
                instanceof Response
        );
        $gr = self::getMethod('getResponse')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($gr instanceof Response);
    }

    /**
     * testSetGetRouter
     * @covers Nymfonya\Component\Http\Kernel::setRouter
     * @covers Nymfonya\Component\Http\Kernel::getRouter
     */
    public function testSetGetRouter()
    {
        self::getMethod('setRouter')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(
            $this->instance->getService(Router::class)
                instanceof Router
        );
        $gr = self::getMethod('getRouter')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($gr instanceof Router);
    }

    /**
     * testSetGetConfig
     * @covers Nymfonya\Component\Http\Kernel::init
     * @covers Nymfonya\Component\Http\Kernel::setConfig
     * @covers Nymfonya\Component\Http\Kernel::getConfig
     * @covers Nymfonya\Component\Http\Kernel::getPath
     */
    public function testSetGetConfig()
    {
        $kp = __DIR__ . self::KERNEL_PATH;
        self::getMethod('init')->invokeArgs(
            $this->instance,
            [Config::ENV_CLI, $kp]
        );
        self::getMethod('setConfig')->invokeArgs(
            $this->instance,
            []
        );
        $gc = self::getMethod('getConfig')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($gc instanceof Config);
        $gp = self::getMethod('getPath')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertEquals($gp, $kp);
    }

    /**
     * testSetGetReflector
     * @covers Nymfonya\Component\Http\Kernel::setClassname
     * @covers Nymfonya\Component\Http\Kernel::setReflector
     * @covers Nymfonya\Component\Http\Kernel::getReflector
     * @covers Nymfonya\Component\Http\Kernel::getFinalMethods
     */
    public function testSetGetReflector()
    {
        self::getMethod('setClassname')->invokeArgs(
            $this->instance,
            [self::CTRL_ACT]
        );
        self::getMethod('setReflector')->invokeArgs(
            $this->instance,
            []
        );
        $gr = self::getMethod('getReflector')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($gr instanceof \ReflectionClass);
        $fms = self::getMethod('getFinalMethods')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_array($fms));
    }

    /**
     * testGetSetActions
     * @covers Nymfonya\Component\Http\Kernel::setClassname
     * @covers Nymfonya\Component\Http\Kernel::getClassname
     * @covers Nymfonya\Component\Http\Kernel::setReflector
     * @covers Nymfonya\Component\Http\Kernel::setActions
     * @covers Nymfonya\Component\Http\Kernel::getActions
     */
    public function testGetSetActions()
    {
        $gc0 = self::getMethod('getClassname')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_string($gc0));
        $this->assertEquals('', $gc0);
        self::getMethod('setClassname')->invokeArgs(
            $this->instance,
            [self::CTRL_ACT]
        );
        $gc1 = self::getMethod('getClassname')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertNotEquals($gc0, $gc1);
        $gas0 = self::getMethod('getActions')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_array($gas0));
        $this->assertEquals([], $gas0);
        self::getMethod('setReflector')->invokeArgs(
            $this->instance,
            []
        );
        self::getMethod('setActions')->invokeArgs(
            $this->instance,
            []
        );
        $gas1 = self::getMethod('getActions')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_array($gas1));
        $this->assertNotEquals($gas1, $gas0);
        $this->assertTrue(count($gas1) > 1);
        $this->assertTrue(in_array('preflight', $gas1));
    }

    /**
     * testSetGetLogger
     * @covers Nymfonya\Component\Http\Kernel::setLogger
     * @covers Nymfonya\Component\Http\Kernel::getLogger
     */
    public function testSetGetLogger()
    {
        self::getMethod('setLogger')->invokeArgs($this->instance, []);
        $lo = self::getMethod('getLogger')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($lo instanceof \Monolog\Logger);
        $hs = $lo->getHandlers();
        $this->assertTrue(is_array($hs));
        $hs0 = $hs[0];
        $this->assertTrue(
            $hs0 instanceof \Monolog\Handler\RotatingFileHandler
        );
    }

    /**
     * testSetGetPath
     * @covers Nymfonya\Component\Http\Kernel::setPath
     * @covers Nymfonya\Component\Http\Kernel::getPath
     */
    public function testSetGetPath()
    {
        $gp0 = self::getMethod('getPath')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertNotEmpty($gp0);
        self::getMethod('setPath')->invokeArgs(
            $this->instance,
            ['']
        );
        $gp1 = self::getMethod('getPath')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertEmpty($gp1);
    }

    /**
     * testGetSetAction
     * @covers Nymfonya\Component\Http\Kernel::setAction
     * @covers Nymfonya\Component\Http\Kernel::getAction
     */
    public function testGetSetAction()
    {
        $ga0 = self::getMethod('getAction')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertEmpty($ga0);
        $this->instance->setAction(self::CTRL_ACT);
        $ga1 = self::getMethod('getAction')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertNotEmpty($ga1);
    }

    /**
     * testIsValidActionOk
     * @covers Nymfonya\Component\Http\Kernel::isValidAction
     * @covers Nymfonya\Component\Http\Kernel::setActions
     * @covers Nymfonya\Component\Http\Kernel::setAction
     */
    public function testIsValidActionOk()
    {
        self::getMethod('setClassname')->invokeArgs(
            $this->instance,
            [self::CTRL_ACT]
        );
        self::getMethod('setReflector')->invokeArgs(
            $this->instance,
            []
        );
        self::getMethod('setActions')->invokeArgs(
            $this->instance,
            []
        );
        $this->instance->setAction(self::CTRL_ACT);
        $iva0 = self::getMethod('isValidAction')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($iva0);
    }

    /**
     * testIsValidActionNok
     * @covers Nymfonya\Component\Http\Kernel::setClassname
     * @covers Nymfonya\Component\Http\Kernel::setReflector
     * @covers Nymfonya\Component\Http\Kernel::setActions
     * @covers Nymfonya\Component\Http\Kernel::setAction
     * @covers Nymfonya\Component\Http\Kernel::isValidAction
     */
    public function testIsValidActionNok()
    {
        self::getMethod('setClassname')->invokeArgs(
            $this->instance,
            [['config']]
        );
        self::getMethod('setReflector')->invokeArgs(
            $this->instance,
            []
        );
        self::getMethod('setActions')->invokeArgs(
            $this->instance,
            []
        );
        $this->instance->setAction(['config']);
        $iva0 = self::getMethod('isValidAction')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertFalse($iva0);
    }

    /**
     * testSetGetActionAnnotations
     * @covers Nymfonya\Component\Http\Kernel::getActionAnnotations
     * @covers Nymfonya\Component\Http\Kernel::setClassname
     * @covers Nymfonya\Component\Http\Kernel::setReflector
     * @covers Nymfonya\Component\Http\Kernel::setActions
     * @covers Nymfonya\Component\Http\Kernel::setAction
     * @covers Nymfonya\Component\Http\Kernel::setActionAnnotations
     */
    public function testSetGetActionAnnotations()
    {
        $gaa0 = self::getMethod('getActionAnnotations')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertEmpty($gaa0);
        $this->assertEquals($gaa0, '');
        self::getMethod('setClassname')->invokeArgs(
            $this->instance,
            [self::CTRL_ACT]
        );
        self::getMethod('setReflector')->invokeArgs(
            $this->instance,
            []
        );
        self::getMethod('setActions')->invokeArgs(
            $this->instance,
            []
        );
        $this->instance->setAction(self::CTRL_ACT);
        $iva = self::getMethod('isValidAction')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($iva);
        self::getMethod('setActionAnnotations')->invokeArgs(
            $this->instance,
            []
        );
        $gaa1 = self::getMethod('getActionAnnotations')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertNotEmpty($gaa1);
    }

    /**
     * testSetMiddleware
     * @covers Nymfonya\Component\Http\Kernel::setMiddleware
     */
    public function testSetMiddleware()
    {
        self::getMethod('setClassname')->invokeArgs(
            $this->instance,
            [self::CTRL_ACT]
        );
        self::getMethod('setReflector')->invokeArgs(
            $this->instance,
            []
        );
        self::getMethod('setActions')->invokeArgs(
            $this->instance,
            []
        );
        $this->instance->setAction(self::CTRL_ACT);
        $iva0 = self::getMethod('isValidAction')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($iva0);
        self::getMethod('setMiddleware')->invokeArgs($this->instance, []);
        $this->assertNotEmpty($this->instance instanceof Kernel);
    }

    /**
     * testExecuteSuccess
     *
     * execute an existing controller action
     *
     * @covers Nymfonya\Component\Http\Kernel::setClassname
     * @covers Nymfonya\Component\Http\Kernel::setReflector
     * @covers Nymfonya\Component\Http\Kernel::setActions
     * @covers Nymfonya\Component\Http\Kernel::getActions
     * @covers Nymfonya\Component\Http\Kernel::setAction
     * @covers Nymfonya\Component\Http\Kernel::isValidAction
     * @covers Nymfonya\Component\Http\Kernel::getClassname
     * @covers Nymfonya\Component\Http\Kernel::setController
     * @covers Nymfonya\Component\Http\Kernel::getController
     * @covers Nymfonya\Component\Http\Kernel::execute
     * @covers Nymfonya\Component\Http\Kernel::getError
     * @covers Nymfonya\Component\Http\Kernel::getErrorMsg
     */
    public function testExecuteSuccess()
    {
        self::getMethod('setClassname')->invokeArgs($this->instance, [self::CTRL_ACT]);
        self::getMethod('setReflector')->invokeArgs($this->instance, []);
        self::getMethod('setActions')->invokeArgs($this->instance, []);
        $gas = self::getMethod('getActions')->invokeArgs($this->instance, []);
        $this->assertNotEmpty($gas);
        $this->assertTrue(is_array($gas));
        $expectedActions = self::CTRL_ACTIONS;
        sort($expectedActions);
        sort($gas);
        $this->assertEquals($gas, $expectedActions);
        $this->instance->setAction(self::CTRL_ACT);
        $this->assertTrue($this->instance instanceof Kernel);
        $this->assertTrue(
            self::getMethod('isValidAction')->invokeArgs($this->instance, [])
        );
        $this->assertTrue(class_exists(
            self::getMethod('getClassname')->invokeArgs($this->instance, [])
        ));
        self::getMethod('setController')->invokeArgs($this->instance, []);
        $this->assertTrue(is_object(
            self::getMethod('getController')->invokeArgs($this->instance, [])
        ));
        self::getMethod('execute')->invokeArgs($this->instance, []);
        $this->assertEquals(
            self::getMethod('getErrorMsg')->invokeArgs($this->instance, []),
            'Execute success'
        );
        $this->assertFalse(
            self::getMethod('getError')->invokeArgs($this->instance, [])
        );
    }

    /**
     * testExecuteFailed
     *
     * execute an existing controller but unknown action
     *
     * @covers Nymfonya\Component\Http\Kernel::setClassname
     * @covers Nymfonya\Component\Http\Kernel::setReflector
     * @covers Nymfonya\Component\Http\Kernel::setActions
     * @covers Nymfonya\Component\Http\Kernel::getActions
     * @covers Nymfonya\Component\Http\Kernel::setAction
     * @covers Nymfonya\Component\Http\Kernel::isValidAction
     * @covers Nymfonya\Component\Http\Kernel::getClassname
     * @covers Nymfonya\Component\Http\Kernel::setController
     * @covers Nymfonya\Component\Http\Kernel::getController
     * @covers Nymfonya\Component\Http\Kernel::execute
     * @covers Nymfonya\Component\Http\Kernel::getError
     * @covers Nymfonya\Component\Http\Kernel::getErrorMsg
     */
    public function testExecuteFailed()
    {
        self::getMethod('setClassname')->invokeArgs($this->instance, [self::CTRL_ACT]);
        self::getMethod('setReflector')->invokeArgs($this->instance, []);
        self::getMethod('setActions')->invokeArgs($this->instance, []);
        $gas = self::getMethod('getActions')->invokeArgs($this->instance, []);
        $this->assertNotEmpty($gas);
        $this->assertTrue(is_array($gas));
        $expectedActions = self::CTRL_ACTIONS;
        sort($expectedActions);
        sort($gas);
        $this->assertEquals($gas, $expectedActions);
        $this->instance->setAction(['config', 'badaction']);
        $iva0 = self::getMethod('isValidAction')->invokeArgs($this->instance, []);
        $this->assertFalse($iva0);
        $cla = self::getMethod('getClassname')->invokeArgs($this->instance, []);
        $this->assertTrue(class_exists($cla));
        self::getMethod('setController')->invokeArgs($this->instance, []);
        $gctr = self::getMethod('getController')->invokeArgs($this->instance, []);
        $this->assertTrue(is_object($gctr));
        self::getMethod('execute')->invokeArgs($this->instance, []);
        $gerr = self::getMethod('getError')->invokeArgs($this->instance, []);
        $germ = self::getMethod('getErrorMsg')->invokeArgs($this->instance, []);
        $this->assertEquals($germ, 'Unknown endpoint');
        $this->assertTrue($gerr);
        $this->assertTrue($this->instance instanceof Kernel);
        self::getMethod('execute')->invokeArgs($this->instance, []);
        $gerr1 = self::getMethod('getError')->invokeArgs($this->instance, []);
        $germ1 = self::getMethod('getErrorMsg')->invokeArgs($this->instance, []);
        $this->assertEquals($germ1, 'Unknown endpoint');
        $this->assertTrue($gerr1);
        $this->assertTrue($this->instance instanceof Kernel);
    }

    /**
     * testInvokeAction
     * @covers Nymfonya\Component\Http\Kernel::setClassname
     * @covers Nymfonya\Component\Http\Kernel::setReflector
     * @covers Nymfonya\Component\Http\Kernel::setActions
     * @covers Nymfonya\Component\Http\Kernel::setAction
     * @covers Nymfonya\Component\Http\Kernel::isValidAction
     * @covers Nymfonya\Component\Http\Kernel::getClassname
     * @covers Nymfonya\Component\Http\Kernel::setController
     * @covers Nymfonya\Component\Http\Kernel::invokeAction
     */
    public function testInvokeAction()
    {
        self::getMethod('setClassname')->invokeArgs($this->instance, [self::CTRL_ACT]);
        self::getMethod('setReflector')->invokeArgs($this->instance, []);
        self::getMethod('setActions')->invokeArgs($this->instance, []);
        $this->instance->setAction(self::CTRL_ACT);
        self::getMethod('setController')->invokeArgs($this->instance, []);
        $ia0 = self::getMethod('invokeAction')->invokeArgs($this->instance, [false]);
        $this->assertTrue(is_object($ia0));
        $ia1 = self::getMethod('invokeAction')->invokeArgs($this->instance, []);
        $this->assertTrue(is_object($ia1));
    }

    /**
     * testExecuteInternalError
     *
     * execute an existing controller but unknown action
     *
     * @covers Nymfonya\Component\Http\Kernel::setClassname
     * @covers Nymfonya\Component\Http\Kernel::setReflector
     * @covers Nymfonya\Component\Http\Kernel::setActions
     * @covers Nymfonya\Component\Http\Kernel::getActions
     * @covers Nymfonya\Component\Http\Kernel::setAction
     * @covers Nymfonya\Component\Http\Kernel::isValidAction
     * @covers Nymfonya\Component\Http\Kernel::getClassname
     * @covers Nymfonya\Component\Http\Kernel::setController
     * @covers Nymfonya\Component\Http\Kernel::getController
     * @covers Nymfonya\Component\Http\Kernel::execute
     * @covers Nymfonya\Component\Http\Kernel::getError
     * @covers Nymfonya\Component\Http\Kernel::getErrorMsg
     */
    public function testExecuteInternalError()
    {
        self::getMethod('setClassname')->invokeArgs($this->instance, [self::CTRL_ACT]);
        self::getMethod('setReflector')->invokeArgs($this->instance, []);
        self::getMethod('setActions')->invokeArgs($this->instance, []);
        $gas = self::getMethod('getActions')->invokeArgs($this->instance, []);
        $this->assertNotEmpty($gas);
        $this->assertTrue(is_array($gas));
        $expectedActions = self::CTRL_ACTIONS;
        sort($expectedActions);
        sort($gas);
        $this->assertEquals($gas, $expectedActions);
        $this->instance->setAction(['config', 'false']);
        $iva0 = self::getMethod('isValidAction')->invokeArgs($this->instance, []);
        $this->assertTrue($iva0);
        $cla = self::getMethod('getClassname')->invokeArgs($this->instance, []);
        $this->assertTrue(class_exists($cla));
        self::getMethod('setController')->invokeArgs($this->instance, []);
        $gctr = self::getMethod('getController')->invokeArgs($this->instance, []);
        $this->assertTrue(is_object($gctr));
        self::getMethod('execute')->invokeArgs($this->instance, []);
        $gerr1 = self::getMethod('getError')->invokeArgs($this->instance, []);
        $this->assertTrue($gerr1);
        $germ1 = self::getMethod('getErrorMsg')->invokeArgs($this->instance, []);
        $this->assertEquals($germ1, 'Execute failed');
        $this->assertTrue($this->instance instanceof Kernel);
    }

    /**
     * testShutdown
     * @covers Nymfonya\Component\Http\Kernel::shutdown
     * @runInSeparateProcess
     */
    public function testShutdown()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(10);
        $this->instance->shutdown(10);
        $this->assertTrue($this->instance instanceof Kernel);
    }
}
