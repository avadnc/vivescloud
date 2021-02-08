# Object

This class should be used as base class for [nearly] every class.

```php
use PO\Hash;

class MyClass extends Object {}

$object = new MyClass;
```

- ```getClass```
```php
$object->getClass(); // MyClass
```

- ```methodMissing``` - method to be overriden in order to implement __call
- ```respondTo``` - refer to __respondTo__
- ```send``` - refer to __send__
- ```toString``` - the __toString() method

## Final methods
- ```__respondTo($method)``` - if implements method
- ```__send($method[, $arg1[, $arg2]])``` - dynamically calls method
