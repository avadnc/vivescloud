<?php

namespace PO;

use InvalidArgumentException;
use ArrayAccess;
use Iterator;
use Countable;

/**
 * @author Marcelo Jacobus <marcelo.jacobus@gmail.com>
 */
class Hash extends Object implements ArrayAccess, Iterator, Countable
{

    /**
     * @var array
     */
    protected $values = array();

    /**
     * @param array   $values    The values to initially set to the Hash
     * @param boolean $recursive Whether to recursively transform arrays into
     *                           Objects
     */
    public function __construct(array $values = array(), $recursive = true)
    {
        if ($recursive) {
            foreach ($values as $key => $value) {
                if (is_array($value)) {
                    $values[$key] = $this->create($value, $recursive);
                }
            }
        }

        $this->values = $values;
    }

    /**
     * Converts hash to array
     * @param  boolean $recursive defaults to true
     * @return array
     */
    public function toArray($recursive = true)
    {
        $values = $this->values;

        if (!$recursive) {
            return $values;
        }

        foreach ($values as $key => $value) {
            if (gettype($value) === 'object') {
                if ($value instanceof Hash) {
                    $value = $value->toArray($recursive);
                }
            }

            $values[$key] = $value;
        }

        return $values;
    }

    /**
     * ArrayAccess implementation
     */
    public function offsetGet($key, $default = null)
    {
        return isset($this->values[$key]) ? $this->values[$key] : $default;
    }

    /**
     * ArrayAccess implementation
     */
    public function offsetSet($key, $value)
    {
        if (is_null($key)) {
            $this->values[] = $value;
        } else {
            $this->values[$key] = $value;
        }

        return $this;
    }

    /**
     * ArrayAccess implementation
     */
    public function offsetExists($key)
    {
        return isset($this->values[$key]);
    }

    /**
     * ArrayAccess implementation
     */
    public function offsetUnset($key)
    {
        unset($this->values[$key]);

        return $this;
    }

    /**
     * Iterator implementation
     */
    public function current()
    {
        return current($this->values);
    }

    /**
     * Iterator implementation
     */
    public function next()
    {
        return next($this->values);
    }

    /**
     * Iterator implementation
     */
    public function key()
    {
        return key($this->values);
    }

    /**
     * Iterator implementation
     */
    public function rewind()
    {
        reset($this->values);

        return $this;
    }

    /**
     * Iterator implementation
     */
    public function valid()
    {
        $key = key($this->values);

        return ($key !== null && $key !== false);
    }

    /**
     * Get a new Hash without elements that have empty or null values
     * @return Hash
     */
    public function compact()
    {
        return $this->reject(function ($value, $key) {
            return $value === '' || $value === null;
        });
    }

    /**
     * Rejects elements if the given function evaluates to true
     *
     * @param $callback callable function
     * @return Hash the new hash containing the non rejected elements
     */
    public function reject($callback)
    {
        $hash = $this->create();

        foreach ($this as $key => $value) {
            if ($callback($value, $key) == false) {
                $hash[$key] = $value;
            }
        }

        return $hash;
    }

    /**
     * Select elements if the given function evaluates to true
     *
     * @param $callback callable function
     * @return Hash the new hash containing the non rejected elements
     */
    public function select($callback)
    {
        $hash = $this->create();

        foreach ($this as $key => $value) {
            if ($callback($value, $key) == true) {
                $hash[$key] = $value;
            }
        }

        return $hash;
    }

    /**
     * A factory for Hash
     *
     * @param  array   $params    the params to create a new object
     * @param  boolean $recursive whether or not to recursive change arrays into
     *                            objects
     * @return Hash
     */
    public static function create(array $params = array(), $recursive = true)
    {
        $class = get_called_class();

        return new $class($params, $recursive);
    }

    /**
     * Maps elements into a new Hash
     *
     * @param  function $callback
     * @return Hash
     */
    public function map($callback)
    {
        $hash = $this->create();

        $this->each(function ($value, $key) use ($callback, $hash) {
            $hash[] = $callback($value, $key);
        });

        return $hash;
    }

    /**
     * Loop the elements of the Hash
     *
     * @param  function $callable
     * @return Hash
     */
    public function each($callable)
    {
        foreach ($this as $key => $value) {
            $callable($value, $key);
        }

        return $this;
    }

    /**
     * Check if has any element
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return $this->count() === 0;
    }

    /**
     * Get the number of elements
     *
     * @return int
     */
    public function count()
    {
        return count($this->toArray());
    }

    /**
     * Get the array keys
     *
     * @return Hash[String] containing the keys
     */
    public function keys()
    {
        return $this->create(array_keys($this->toArray()))->map(
            function ($key) {
                return new String($key);
            }
        );
    }

    /**
     * Check object has given key
     *
     * @param  string $key
     * @return bool
     */
    public function hasKey($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * Gets teh index and removes it from the object
     *
     * @param  string $key
     * @return mixed  the element on the given index
     */
    public function delete($key)
    {
        $element = $this[$key];
        $this->offsetUnset($key);

        return $element;
    }

    /**
     * Get the value by the key. Throws exception when key is not set
     *
     * @param  string                   $key
     * @param  mixed                    $default either value or callable function
     * @return mixed                    the value for the given key
     * @throws InvalidArgumentException
     */
    public function fetch($key, $default = null)
    {
        if ($this->hasKey($key)) {
            return $this[$key];
        } elseif ($default !== null) {

            if (is_callable($default)) {
                return $default($key);
            }

            return $default;
        }

        throw new InvalidArgumentException("Invalid key '$key'");
    }

    /**
     * Get the values at the given indexes.
     *
     * Both work the same:
     *    <code>
     *      $hash->valuesAt(array('a', 'b'));
     *      $hash->valuesAt('a', 'b');
     *    </code>
     *
     * @param mixed keys
     * @return Hash containing the values at the given keys
     */
    public function valuesAt()
    {
        $args = func_get_args();

        if (is_array($args[0])) {
            $args = $args[0];
        }

        $hash = $this->create();

        foreach ($args as $key) {
            $hash[] = $this[$key];
        }

        return $hash;
    }

    /**
     * Join the values of the object
     *
     * @param  string $separator defauts to empty string
     * @return string
     */
    public function join($separator = '')
    {
        return implode($separator, $this->toArray());
    }

    /**
     * Get first element
     * @return mixed
     */
    public function first()
    {
        $array = $this->toArray(false);

        return array_shift($array);
    }

    /**
     * Get the last element
     * @return mixed
     */
    public function last()
    {
        $array = $this->toArray(false);

        return array_pop($array);
    }

    /**
     * Group elements by the given criteria
     *
     * @param  mixed $criteria it can be either a callable function or a string,
     *                         representing a key of an element
     * @return Hash
     */
    public function groupBy($criteria)
    {
        $criteria = $this->factoryCallableCriteria($criteria);
        $groups   = $this->create();

        $this->each(function ($element, $key) use ($groups, $criteria) {
            $groupName  = $criteria($element, $key);
            $elements   = $groups->offsetGet($groupName, array());
            $elements[] = $element;
            $groups[$groupName] = $elements;
        });

        return $groups;
    }

    /**
     * Sort elements by the given criteria
     *
     * @param  mixed $criteria it can be either a callable function or a string,
     *                         representing a key of an element
     * @return Hash
     */
    public function sortBy($criteria)
    {
        $criteria = $this->factoryCallableCriteria($criteria);
        $sorted   = $this->create();
        $groups   = $this->groupBy($criteria);

        $criterias = $this->map(function ($element, $key) use ($criteria) {
            return $criteria($element, $key);
        })->toArray();

        sort($criterias);
        $criterias = array_unique($criterias);

        foreach ($criterias as $key) {
            foreach ($groups[$key] as $element) {
                $sorted[] = $element;
            }
        }

        return $sorted;
    }

    /**
     * Tells if the object includes the given element in the first
     * level of the collection. Strict mode. compares type
     *
     * @return boolean
     */
    public function hasValue($value)
    {
        return in_array($value, $this->toArray(false), true);
    }

    /**
     * TODO: Write some description
     *
     * @param mixed    $memo     callback or the memo. If no memo is given, it must be
     *                           a callable function
     * @param callable $callback the callback receives $injected as first param
     *                           the element value as second param and the
     *                           elemen key as the third param
     *
     * @return mixed
     */
    public function inject($memo = null, $callback = null)
    {
        if ($this->isCallable($callback)) {
            foreach ($this as $key => $value) {
                $memo = $callback($memo, $value, $key);
            }
        } elseif ($this->isCallable($memo)) {
            return $this->inject(null, $memo);
        } else {
            throw new InvalidArgumentException('No callback was given');
        }

        return $memo;
    }

    /**
     * Get a function that returns something based on an element item
     *
     * @mixed $criteria either a callable function that returns a value or a
     *    string that is an element key
     *
     * @return callable
     */
    private function factoryCallableCriteria($criteria)
    {
        if (!$this->isCallable($criteria)) {
            $criteria = function ($element, $key) use ($criteria) {
                return $element->fetch($criteria);
            };
        }

        return $criteria;
    }

    /**
     * Param is function?
     * @param  mixed   $callable
     * @return boolean
     */
    protected function isCallable($callable)
    {
        return gettype($callable) === 'object';
    }
}
