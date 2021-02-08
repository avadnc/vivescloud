<?php

namespace Dummy;

use PO\String;

class Object extends \PO\Object
{

    public function exampleOne()
    {
        return 'example one';
    }

    public function exampleTwo($argument)
    {
        return 'argumets: ' . $argument;
    }

    public function exampleThree($a, $b = null, $c = null)
    {
        $string = new String('argumets: a: ');
        $string->append($a)
            ->append(', b: ')
            ->append($b)
            ->append(', c: ')
            ->append($c);

        return $string->toString();
    }

    protected function protectedMethod()
    {

    }

    private function privateMethod()
    {
    }
}
