# Hash

This is a (work in progress) port of Ruby Hash to PHP. Not all methods will be ported.
The examplified methods are already ported. The non documented methods must be implemented.

Note that the API is not very consistent. You get the ```last``` method, but perhaps the best choice would be ```getLast```.

The methods that have description are the ones that were impplemented. Fill free to write your implementation.

```php
use PO\Hash;
```

- ```clear```
- ```collect``` - Use map instead.
- ```compact``` (not in ruby Hash) - Removes null and empty

```php
$hash = new Hash(array(
    'foo'   => 'bar',
    'null'  => null,
    'empty' => ''
));

$hash->compact()->toArray(); // array('foo' => 'bar')
```

- ```count``` - Get the number of keys

```php
Hash::create(['a' => 'b'])->count(); // 1
```

- ```cycle```
- ```delete``` - Removes the element from the Hash and returns it.

```php
$object = new Something;

$hash = Hash::create(['foo' => $object, 'b' => 'bar']);

$deleted = $hash->delete('foo');

$hash->toArray());   // ['b' => 'bar']

$deleted === $object // true
```

- ```delete_if```
- ```detect```
- ```drop```
- ```drop_while```
- ```each``` - Iterates through the values and keys of the object.

```php
$hash = new Hash(array('a' => 'b', 'c' => 'd'));

$array = new Hash;

$hash->each(function ($value, $key) use ($array) {
    $array[] = $key;
    $array[] = $value;
});

$hash->toArray() // array( 'a', 'b', 'c', 'd');
```

- ```each_cons```
- ```each_entry```
- ```each_key```
- ```each_pair```
- ```each_slice```
- ```each_value```
- ```each_with_index```
- ```each_with_object```
- ```empty``` - Not implemented. Use isEmpty instead.
- ```entries```
- ```fetch``` - Gets the value by the given key. If the key is not set, throws InvalidArgumentException

```php
$hash = Hash::create(['foo' => 'bar']);

$hash->fetch('foo') // bar

$hash->fetch('bar', 'foo') // foo

$hash->fetch('bar') // throws InvalidArgumentException

$hash->fetch('foo', function ($value) {
    return "Value is '$value'";
}); // Value is 'bar'

$hash->fetch('foo', function () {
    return "No value";
}); // No value
```

- ```find```
- ```find_all```
- ```find_index```
- ```first``` - Get the first element
- ```flat_map```
- ```flatten```
- ```grep```
- ```group_by``` - Groups elements by a certain criteria

```php
$foo = new Hash(array('name' => 'foo', 'age' => 20));
$bar = new Hash(array('name' => 'bar', 'age' => 20));
$baz = new Hash(array('name' => 'baz', 'age' => 21));

$hash = new Hash(array($foo, $bar, $baz));

$groups = $hash->groupBy(function ($element) {
    return $element['age'];
})->toArray();

// Will return:
array(
    20 => array($foo, $bar),
    21 => array($baz)
);

// The same result can be achieved with can be done by using the index

$groups = $hash->groupBy('age');

```

- ```hasKey```  - Check if key exists.

```php
$hash->hasKey('foo') // true
```

- ```hasValue``` - Returns true if hash includes the given element
- ```include```?
- ```index```
- ```inject``` - No good description here. TODO: Write some

```php
// Example 1
$hash = new Hash(array(1, 2, 3, 4, 5));
$sum = $hash->inject(0, function ($injected, $element) {
    return $injected += $element;
});

// $sum === 15

// Example 2
$hash = new Hash(array('cat', 'sheep', 'bear'));

$longest = $hash->inject(function ($memo, $word) {
    return (strlen($memo) > strlen($word)) ? $memo : $word;
});

// $longest === 'sheep'
```

- ```invert```
- ```isEmpty``` - Is empty?

```php
Hash::create(['a' => 'b'])->isEmpty(); // false
```
- ```join``` (not in ruby Hash) - Joins element values

```php
$hash = new Hash(array('foo'   => 'bar', 'bar' => 'baz');
$hash->join(', '); // 'bar, baz'
```

- ```keep_if```
- ```key```
- ```key```?
- ```keys``` -  Get the array keys. Return a Hash.

```php
Hash::create(['a' => 'b'])->keys()->toArray(); // array('a')
```

- ```last``` - Get the element
- ```lazy```
- ```length```
- ```map``` - Maps modified elements into a new hash

```php
$hash = new Hash(array(
    'a' => 'b',
    'c' => 'd'
));

$mapped = $hash->map(function ($value, $key) {
    return $key . $value;
})->toArray();

// array('ab', 'cd');
```

- ```max```
- ```max_by```
- ```member```?
- ```merge```
- ```merge```!
- ```min```
- ```min_by```
- ```minmax```
- ```minmax_by```
- ```none```?
- ```one```?
- ```partition```
- ```rassoc```
- ```reduce```
- ```rehash```
- ```reject``` -  New Hash with elements that will not match the given callback

```php
$hash = new Hash(array(
  'foo' => 'foobar',
  'bar' => 'barfoo'
));

$filtered = $hash->reject(function ($value, $key) {
    return $value === 'barfoo';
})->toArray();

// array('foo' ='foobar')
```

- ```reject```!
- ```replace```
- ```reverse_each```
- ```select``` - Get a new Hash with elements that match the given callback

```php
$hash = new Hash(array(
  'foo' => 'foobar',
  'bar' => 'barfoo'
));

$filtered = $hash->select(function ($value, $key) {
    return $value !== 'barfoo';
})->toArray();

// array('foo' ='foobar')
```

- ```select```!
- ```shift```
- ```size```
- ```slice_before```
- ```sort```
- ```sortBy``` - Pass in a criteria and get and get a sorted hash

```php

$first  = new Hash(array('order' => 1));
$second = new Hash(array('order' => 2));
$third  = new Hash(array('order' => 3));
$fourth = new Hash(array('order' => 3));
$fifth  = new Hash(array('order' => 5));

$hash   = new Hash(array($third, $fifth, $second, $first, $fourth));

$sorted = $hash->sortBy(function ($element) {
    return $element['order'];
})->toArray();

// will return

array($first, $second, $third, $fourth, $fifth);

// the same could be acomplished by using the array key as criteria;

$hash->sortBy('order');

```

- ```store```
- ```take```
- ```take_while```
- ```to_a```
- ```to_h```
- ```to_hash```
- ```update```
- ```value```?
- ```values```
- ```values_at``` - Get the values at the given keys.

```php
$hash = new Hash(array('a' => 'b', 'c' => 'b'));

$hash->valuesAt(array('a', 'b'))->toArray(); // array('b', null)

// same as
$hash->valuesAt('a', 'b')->toArray();        // array('b', null)
```

- ```zip```
