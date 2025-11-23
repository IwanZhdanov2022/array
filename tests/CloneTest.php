<?php

use Iwan07\ArrayMethods\ArrayMethods;
use PHPUnit\Framework\TestCase;

class CloneTest extends TestCase
{
    public function testClone()
    {
        $src = ArrayMethods::from([10, 20, [100, 200, 300], [555, 777, 999, 111], 50]);
        $src[2][2] = 333;

        $z = $src[3]->clone();

        $x = $src[3];
        $x[1] = 0;

        $y = $src[3]->clone();
        $y[2] = 987;

        $z[0] = 'abcde';

        $src[0] = ['a', 'b', 'c'];

        $this->assertEquals([['a', 'b', 'c'], 20, [100, 200, 333], [555, 0, 999, 111], 50], $src->get());
        $this->assertEquals([555, 0, 999, 111], $x->get());
        $this->assertEquals([555, 0, 987, 111], $y->get());
        $this->assertEquals(['abcde', 777, 999, 111], $z->get());
    }

    public function testFilter()
    {
        $src = ArrayMethods::from([10, 20, 30, 40, 50]);
        $cloned = $src->filter(fn($x) => $x >= 40);
        $cloned[0] = 12;
        $cloned[1] = 34;
        $this->assertEquals([10, 20, 30, 40, 50], $src->get());

        $src = ArrayMethods::from([10, ['id' => 20, 'value' => 11111], 30, 40, ['id' => 50, 'value' => 22222]]);
        $filtered = $src->filter(fn($x) => $x instanceof ArrayMethods)->values();
        print_r($filtered->get());
        $filtered[0]['value'] = 8888;
        $filtered[1]['value'] = 9999;
        $this->assertEquals([10, ['id' => 20, 'value' => 8888], 30, 40, ['id' => 50, 'value' => 9999]], $filtered->get());
    }
}
