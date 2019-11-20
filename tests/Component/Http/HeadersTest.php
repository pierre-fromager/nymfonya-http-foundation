<?php

namespace Tests\Component\Http;

use PHPUnit\Framework\TestCase as PFT;
use Nymfonya\Component\Http\Headers;

/**
 * @covers \Nymfonya\Component\Http\Headers::<public>
 */
class HeadersTest extends PFT
{

    const TEST_ENABLE = true;

    /**
     * instance
     *
     * @var Headers
     */
    protected $instance;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if (!self::TEST_ENABLE) {
            $this->markTestSkipped('Test disabled.');
        }
        $this->instance = new Headers();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
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
        $class = new \ReflectionClass(Headers::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers Nymfonya\Component\Http\Headers::__construct
     * @covers Nymfonya\Component\Http\Headers::get
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Headers);
        $this->assertEquals($this->instance->get(), []);
    }

    /**
     * constantsProvider
     * @return Array
     */
    public function constantsProvider()
    {
        return [
            ['CONTENT_TYPE'],
            ['CONTENT_LENGTH'],
            ['ACCEPT_ENCODING'],
            ['HEADER_ACA'],
            ['HEADER_ACA_ORIGIN'],
            ['HEADER_ACA_CREDENTIALS'],
            ['HEADER_ACA_METHODS'],
            ['HEADER_ACA_HEADERS'],
        ];
    }

    /**
     * testConstants
     * @covers Nymfonya\Component\Http\Headers::__construct
     * @dataProvider constantsProvider
     */
    public function testConstants($k)
    {
        $class = new \ReflectionClass(Headers::class);
        $this->assertArrayHasKey($k, $class->getConstants());
        unset($class);
    }

    /**
     * testAdd
     * @covers Nymfonya\Component\Http\Headers::add
     * @covers Nymfonya\Component\Http\Headers::get
     */
    public function testAdd()
    {
        $this->instance->add('testkey', 'testvalue');
        $this->assertEquals(
            $this->instance->get(),
            ['testkey' => 'testvalue']
        );
    }

    /**
     * testAddMany
     * @covers Nymfonya\Component\Http\Headers::add
     * @covers Nymfonya\Component\Http\Headers::get
     */
    public function testAddMany()
    {
        $headers = [
            'testkey' => 'testvalue',
            'testkey1' => 'testvalue1',
        ];
        $this->instance->addMany($headers);
        $this->assertEquals($this->instance->get(), $headers);
    }

    /**
     * testRemove
     * @covers Nymfonya\Component\Http\Headers::remove
     * @covers Nymfonya\Component\Http\Headers::get
     */
    public function testRemove()
    {
        $headers = [
            'testkey' => 'testvalue',
            'testkey1' => 'testvalue1',
        ];
        $this->instance->addMany($headers);
        $this->instance->remove('testkey');
        $this->assertEquals(
            $this->instance->get(),
            ['testkey1' => 'testvalue1']
        );
    }

    /**
     * testSend
     * @covers Nymfonya\Component\Http\Headers::addMany
     * @covers Nymfonya\Component\Http\Headers::send
     * @runInSeparateProcess
     */
    public function testSend()
    {
        $headers = ['hk' => 'hv'];
        $this->instance->addMany($headers);
        $this->assertTrue(
            $this->instance->send() instanceof Headers
        );
    }
}
