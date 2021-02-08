<?php

namespace POTests;

use PO\Object;
use Dummy\Object as Dummy;

/**
 * @author Marcelo Jacobus <marcelo.jacobus@gmail.com>
 */
class ObjectTest extends \PHPUnit_Framework_TestCase
{

    public function testCanGetClass()
    {
        $object = new Dummy;
        $this->assertEquals('Dummy\Object', $object->getClass());
    }

    /**
     * @expectedException \PO\NoMethodException
     * @expectedExceptionMessage \
     *    Undefined method 'unexistingMethod' for Dummy\Object
     */
    public function testItThrowsExceptionOnMethodMissing()
    {
        $object = new Dummy;
        $object->unexistingMethod();
    }

    public function testSendWithNoArguments()
    {
        $object = new Dummy;
        $this->assertEquals('example one', $object->send('exampleOne'));
    }

    public function testSendWithOneArgument()
    {
        $object = new Dummy;
        $return = $object->send('exampleTwo', 'abc');
        $this->assertEquals('argumets: abc', $return);
    }

    public function testSendWithSeveralArgument()
    {
        $object = new Dummy;
        $return = $object->send('exampleThree', 'a', 'b', 'c');

        $this->assertEquals(
            'argumets: a: a, b: b, c: c',
            $return
        );
    }

    public function testRespondTo()
    {
        $object = new Dummy;
        $this->assertTrue($object->respondTo('exampleOne'));
        $this->assertFalse($object->respondTo('undefinedMethod'));
    }

    public function testGetMethods()
    {
        $object = new Dummy;

        $methods = array('exampleOne', 'exampleTwo', 'exampleThree');

        $objectMethods = $object->getMethods()->toArray();

        foreach ($methods as $method) {
            $this->assertTrue(in_array($method, $objectMethods));
        }

    }
}
