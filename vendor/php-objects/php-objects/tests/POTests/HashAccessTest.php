<?php

namespace POTests;

use PO\Hash;

/**
 * @author Marcelo Jacobus <marcelo.jacobus@gmail.com>
 */
class HashAccessTest extends HashTest
{

    public function testItCanBeAccessWithHash()
    {
        $this->assertFalse(isset($this->o['foo']));
        $this->o['foo'] = 'bar';
        $this->assertTrue(isset($this->o['foo'])).
        $this->assertEquals('bar', $this->o['foo']);
    }

    public function testItImplementsIterator()
    {
        $params = array('foo' => 'bar', 'jon' => 'doe');
        $hash = new Hash($params);

        $values = array();

        foreach ($hash as $key => $value) {
            $values[$key] = $value;
        }

        $this->assertEquals($params, $values);
    }

    public function testItCanGetDefaultValues()
    {
        $object = new Hash;
        $this->assertNull($object['foo']);
        $this->assertEquals('bar', $object->offsetGet('foo', 'bar'));
    }

    public function testIsCanUnsetKey()
    {
        $hash = new Hash(array('a' => 'b', 'b' => 'c' ));

        $hash->offsetUnset('a');
        $this->assertEquals(array('b' => 'c'), $hash->toArray());

        unset($hash['b']);
        $this->assertEquals(array(), $hash->toArray());
    }

    public function testItIsConsideredAnArray()
    {
        $this->markTestSkipped('Perhaps it is not possible');
        $this->assertTrue(is_array(new Hash));
    }

    public function testItCanBeConvertedToArray()
    {
        $this->markTestSkipped('Perhaps it is not possible');
        $array = array('a' => 'b', 'b' => 'c' );
        $hash = new Hash($array);
        $this->assertEquals($array, Dummy\Type::toArray($hash));
    }

    public function testItInterchangeWithArray()
    {
        $this->markTestSkipped('Perhaps it is not possible');
        $hash = new Hash();
        Dummy\Type::requireArray($hash);
    }
}
