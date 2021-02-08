<?php

namespace POTests;

use PO\Hash;

/**
 * @author Marcelo Jacobus <marcelo.jacobus@gmail.com>
 */
class HashTest extends \PHPUnit_Framework_TestCase
{

    public function assertHash($object)
    {
        // $object = new Hash;
        $this->assertInstanceOf('PO\Hash', $object);
    }

    /**
     * @covers PO\Hash::compact()
     */
    public function testItCanCampactAHash()
    {
        $hash = new Hash(array('foo' => 'bar', 'null' => null, 'empty' => ''));
        $compact = $hash->compact();
        $this->assertEquals(array('foo' => 'bar'), $compact->toArray());
        $this->assertHash($compact);
    }

    /**
     * @covers PO\Hash::reject()
     */
    public function testItCanRejectElementsByValue()
    {
        $hash = new Hash(array('foo' => 'foobar', 'bar' => 'barfoo'));

        $filtered = $hash->reject(function ($value, $key) {
            return $value === 'barfoo';
        });

        $this->assertEquals(array('foo' => 'foobar'), $filtered->toArray());
        $this->assertHash($filtered);
    }

    /**
     * @covers PO\Hash::reject()
     */
    public function testItCanRejectElementsByKey()
    {
        $hash = new Hash(array('foo' => 'foobar', 'bar' => 'barfoo'));

        $filtered = $hash->reject(function ($value, $key) {
            return $key === 'bar';
        });

        $this->assertEquals(array('foo' => 'foobar'), $filtered->toArray());
        $this->assertHash($filtered);
    }

    /**
     * @covers PO\Hash::select()
     */
    public function testItCanSelectElementsByValue()
    {
        $hash = new Hash(array('foo' => 'foobar', 'bar' => 'barfoo'));

        $filtered = $hash->select(function ($value, $key) {
            return $value !== 'barfoo';
        });

        $this->assertEquals(array('foo' => 'foobar'), $filtered->toArray());
        $this->assertHash($filtered);
    }

    /**
     * @covers PO\Hash::select()
     */
    public function testItCanSelectElementsByKey()
    {
        $hash = new Hash(array('foo' => 'foobar', 'bar' => 'barfoo'));

        $filtered = $hash->select(function ($value, $key) {
            return $key === 'foo';
        });

        $this->assertEquals(array('foo' => 'foobar'), $filtered->toArray());
        $this->assertHash($filtered);
    }

    /**
     * @covers PO\Hash::map()
     */
    public function testItCanMapElements()
    {
        $hash = new Hash(array('a' => 'b', 'c' => 'd'));

        $mapped = $hash->map(function ($value) {
            return $value;
        });

        $expectation = array('b', 'd');

        $this->assertEquals($expectation, $mapped->toArray());
    }

    /**
     * @covers PO\Hash::each()
     */
    public function testItIterateViaEach()
    {
        $hash = new Hash(array('a' => 'b', 'c' => 'd'));

        $array = new Hash;

        $hash->each(function ($value) use ($array) {
            $array[] = $value;
        })->each(function ($value) use ($array) {
            $array[] = $value;
        });

        $expectation = array( 'b', 'd', 'b', 'd');

        $this->assertEquals($expectation, $array->toArray());
    }

    /**
     * @covers PO\Hash::each()
     */
    public function testItIterateViaEachWithIndex()
    {
        $hash = new Hash(array('a' => 'b', 'c' => 'd'));

        $array = new Hash;

        $hash->each(function ($value, $key) use ($array) {
            $array[] = $key;
            $array[] = $value;
        })->each(function ($value, $key) use ($array) {
            $array[] = $key;
            $array[] = $value;
        });

        $expectation = array(
            'a', 'b', 'c', 'd',
            'a', 'b', 'c', 'd',
        );

        $this->assertEquals($expectation, $array->toArray());
    }

    /**
     * @covers PO\Hash::create()
     */
    public function testItCanFactoryTheCorrectClass()
    {
        $hash = new \Dummy\Hash;
        $created = $hash->create();
        $this->assertInstanceOf('\Dummy\Hash', $created);

        $params = array('a' => 'b');
        $created = $hash->create($params);
        $this->assertEquals($params, $created->toArray());
    }

    /**
     * @covers PO\Hash::create()
     */
    public function testFactoryCanRecursivelyInstantiateHashes()
    {
        $params = array(
            'user' => array(
                'city' => array(
                    'name' => 'Novo Hamburgo',
                    'state' => array(
                        'name' => 'RS'
                    )
                )
            )
        );

        $hash = \Dummy\Hash::create($params);

        $this->assertEquals('Novo Hamburgo', $hash['user']['city']['name']);
        $this->assertEquals('RS', $hash['user']['city']['state']['name']);

        $hash = $hash->fetch('user');
        $this->assertInstanceOf('\Dummy\Hash', $hash);

        $hash = $hash->fetch('city');
        $this->assertInstanceOf('\Dummy\Hash', $hash);

        $hash = $hash->fetch('state');
        $this->assertInstanceOf('\Dummy\Hash', $hash);
    }

    /**
     * @covers PO\Hash::isEmpty()
     */
    public function testIsEmpty()
    {
        $hash = new Hash(array('foo' => 'bar'));

        $this->assertFalse($hash->isEmpty());
        $this->assertFalse(empty($hash));

        unset($hash['foo']);

        $this->assertTrue($hash->isEmpty());
        // $this->assertTrue(empty($hash));
    }

    /**
     * @covers PO\Hash::count()
     */
    public function testCount()
    {
        $hash = new Hash(array('foo' => 'bar'));

        $this->assertEquals(1, $hash->count());
        $this->assertEquals(1, count($hash));

        unset($hash['foo']);

        $this->assertEquals(0, $hash->count());
        $this->assertEquals(0, count($hash));
    }

    /**
     * @covers PO\Hash::keys()
     */
    public function testKeys()
    {
        $hash = new Hash(
            array(
                'foo' => 'foobar',
                'bar' => 'barfoo'
            )
        );

        $expected = array('foo', 'bar');

        $this->assertEquals($expected, $hash->keys()->toArray());
        $this->assertInstanceOf('PO\String', $hash->keys()->first());
    }

    /**
     * @covers PO\Hash::hasKey()
     */
    public function testHasKey()
    {
        $hash = new Hash(array('foo' => 'foobar'));

        $this->assertTrue($hash->hasKey('foo'));
        $this->assertFalse($hash->hasKey('bar'));
    }

    /**
     * @covers PO\Hash::delete()
     */
    public function testDelete()
    {
        $object = new Hash;
        $hash = Hash::create(array('foo' => $object, 'b' => 'bar'));
        $deleted = $hash->delete('foo');

        $this->assertSame($object, $deleted);
        $this->assertEquals(array('b' => 'bar'), $hash->toArray());
    }

    /**
     * @covers PO\Hash::fetch()
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid key 'bar'
     */
    public function testFetch()
    {
        $hash = Hash::create(array('foo' => 'bar'));
        $this->assertEquals('bar', $hash->fetch('foo'));
        $this->assertEquals('bar', $hash->fetch('foo', 'default'));
        $hash->fetch('bar');
    }

    /**
     * @covers PO\Hash::fetch()
     */
    public function testFetchReturnsDefaultValue()
    {
        $hash = Hash::create();
        $this->assertEquals('bar', $hash->fetch('foo', 'bar'));
    }

    /**
     * @covers PO\Hash::fetch()
     */
    public function testFetchAcceptsLambdaAsDefaultValue()
    {
        $hash = Hash::create(array('foo' => 'bar'));

        $function = function ($key) {
            return "default for '$key'";
        };

        $this->assertEquals("bar", $hash->fetch('foo', $function));
        $this->assertEquals("default for 'k'", $hash->fetch('k', $function));
    }

    /**
     * @covers PO\Hash::valuesAt()
     */
    public function testValuesAt()
    {
        $hash = new Hash(array('a' => 'b', 'c' => 'b'));

        $expected = array('b', null);

        $this->assertEquals(
            $expected,
            $hash->valuesAt(array('a', 'b'))->toArray()
        );
        $this->assertEquals($expected, $hash->valuesAt('a', 'b')->toArray());
    }

    /**
     * @covers PO\Hash::join()
     */
    public function testJoin()
    {
        $hash = new Hash(array('a' => 'b', 'c' => 'd' ));

        $this->assertEquals('bd', $hash->join());
        $this->assertEquals('b, d', $hash->join(', '));
    }

    /**
     * @covers PO\Hash::first()
     */
    public function testFirst()
    {
        $first = new Hash;
        $hash = new Hash(array($first, 'b'));
        $this->assertSame($first, $hash->first());
    }

    /**
     * @covers PO\Hash::last()
     */
    public function testLast()
    {
        $last = new Hash;
        $hash = new Hash(array('a', $last));
        $this->assertEquals($last, $hash->last());
    }

    /**
     * @covers PO\Hash::groupBy()
     */
    public function testGroupByWithCallableObject()
    {
        $foo = new Hash(array('name' => 'foo', 'age' => 20));
        $bar = new Hash(array('name' => 'bar', 'age' => 20));
        $baz = new Hash(array('name' => 'baz', 'age' => 21));

        $hash = new Hash(array($foo, $bar, $baz));

        $groups = $hash->groupBy(function ($element) {
            return $element['age'];
        });

        $expected = array(
            20 => array($foo, $bar),
            21 => array($baz)
        );

        $this->assertEquals($expected, $groups->toArray());
    }

    /**
     * @covers PO\Hash::groupBy()
     */
    public function testGroupByWithKey()
    {
        $foo = new Hash(array('name' => 'foo', 'age' => 20));
        $bar = new Hash(array('name' => 'bar', 'age' => 20));
        $baz = new Hash(array('name' => 'baz', 'age' => 21));

        $hash = new Hash(array($foo, $bar, $baz));

        $groups = $hash->groupBy('age');

        $expected = array(
            20 => array($foo, $bar),
            21 => array($baz)
        );

        $this->assertEquals($expected, $groups->toArray());
    }

    protected function orderTest($order)
    {
        $first  = new Hash(array('order' => 1));
        $second = new Hash(array('order' => 2));
        $third  = new Hash(array('order' => 3));
        $fourth = new Hash(array('order' => 3));
        $fifth  = new Hash(array('order' => 5));

        $hash   = new Hash(array($third, $fifth, $second, $first, $fourth));

        $sorted = $hash->sortBy($order);

        $expected = array($first, $second, $third, $fourth, $fifth);

        $this->assertEquals($expected, $sorted->toArray(false));
    }

    /**
     * @covers PO\Hash::sortBy()
     */
    public function testSortByWithCallableObject()
    {
        $this->orderTest(function ($element) {
            return $element['order'];
        });
    }

    /**
     * @covers PO\Hash::sortBy()
     */
    public function testSortByWithStringParam()
    {
        $this->orderTest('order');
    }

    /**
     * @covers PO\Hash::toArray()
     */
    public function testToArray()
    {
        $params = array(
            'foo' => array(
                'bar' => array(
                    'baz' => 'foobar'
                )
            )
        );

        $hash = Hash::create($params);

        $this->assertEquals($params, $hash->toArray());
    }

    public function testHasValue()
    {
        $foo = new Hash;
        $bar = new Hash;
        $baz = new Hash;

        $hash = Hash::create(
            array(
                $foo,
                'a'      => 'b',
                'bar'    => $bar,
                'number' => '2'
            )
        );

        $this->assertTrue($hash->hasValue('b'));
        $this->assertTrue($hash->hasValue($foo));
        $this->assertTrue($hash->hasValue($bar));
        $this->assertFalse($hash->hasValue(2));
        $this->assertFalse($hash->hasValue('a'));
        $this->assertFalse($hash->hasValue($baz));
    }

    public function testInject()
    {
        $hash = new Hash(array(1, 2, 3, 4, 5));

        $this->assertEquals(15, $hash->inject(0, function ($injected, $element) {
            return $injected += $element;
        }));

        $this->assertEquals(16, $hash->inject(1, function ($injected, $element) {
            return $injected += $element;
        }));
    }

    public function testInjectWithNoInjectedValue()
    {
        $hash = new Hash(array('cat', 'sheep', 'bear'));

        $longest = $hash->inject(function ($memo, $word) {
            return (strlen($memo) > strlen($word)) ? $memo : $word;
        });

        $this->assertEquals('sheep', $longest);
    }
}
