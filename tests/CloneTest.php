<?php

use Iwan07\ArrayMethods\ArrayMethods;
use PHPUnit\Framework\TestCase;

class CloneTest extends TestCase
{
    public function testClone()
    {
        $src = ArrayMethods::from([10, 20, [100, 200, 300], [555, 777, 999, 111], 50]);
        $src[2][2] = 333;

        $z = clone $src[3];

        $x = $src[3];
        $x[1] = 0;

        $y = clone $src[3];
        $y[2] = 987;

        $z[0] = 'abcde';

        $src[0] = ['a', 'b', 'c'];

        $this->assertEquals([['a', 'b', 'c'], 20, [100, 200, 333], [555, 0, 999, 111], 50], $src->get());
        $this->assertEquals([555, 0, 999, 111], $x->get());
        $this->assertEquals([555, 0, 987, 111], $y->get());
        $this->assertEquals(['abcde', 777, 999, 111], $z->get());
    }

    public function testDeepClone()
    {
        $src = ArrayMethods::from([
            'a' => [
                'b' => [
                    'c' => 100,
                ],
            ],
        ]);
        $cloned = clone $src;

        $src['a']['b']['c'] = 200;
        $cloned['a']['b']['c'] = 300;

        $this->assertEquals(['a' => ['b' => ['c' => 200]]], $src->get());
        $this->assertEquals(['a' => ['b' => ['c' => 300]]], $cloned->get());
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
        $filtered[0]['value'] = 8888;
        $filtered[1]['value'] = 9999;
        $this->assertEquals([['id' => 20, 'value' => 8888], ['id' => 50, 'value' => 9999]], $filtered->get());
    }
}
