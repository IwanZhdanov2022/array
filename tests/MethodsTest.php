<?php

use Iwan07\ArrayMethods\ArrayMethods;
use PHPUnit\Framework\TestCase;

class MethodsTest extends TestCase
{
    protected ArrayMethods $src;

    public function testGenerate()
    {
        $a1 = ArrayMethods::generate(3);
        $this->assertEquals([0 => null, 1 => null, 2 => null], $a1->get());
        $a2 = ArrayMethods::generate(5, 'abc');
        $this->assertEquals([0 => 'abc', 1 => 'abc', 2 => 'abc', 3 => 'abc', 4 => 'abc'], $a2->get());
        $a3 = ArrayMethods::generate(6, fn($i) => $i + 1);
        $this->assertEquals([0 => 1, 1 => 2, 2 => 3, 3 => 4, 4 => 5, 5 => 6], $a3->get());
    }

    public function testFilter()
    {
        $arr = [3, 7, 10, 34, 73, 50];
        $dest = ArrayMethods::from($arr)->filter(fn($x) => $x != 34);
        $this->assertEquals([0 => 3, 1 => 7, 2 => 10, 4 => 73, 5 => 50], $dest->get());
    }

    public function testMap()
    {
        $arr = [3, 7, 10, 34, 73, 50];
        $dest = ArrayMethods::from($arr)->map(fn($x) => $x ** 2);
        $this->assertEquals([9, 49, 100, 1156, 5329, 2500], $dest->get());
    }

    public function testMapKeys()
    {
        $arr = [3, 7, 10, 34, 73, 50];
        $dest = ArrayMethods::from($arr)->mapKeys(fn($x) => $x);
        $this->assertEquals([3 => 3, 7 => 7, 10 => 10, 34 => 34, 73 => 73, 50 => 50], $dest->get());
    }

    public function testReduce()
    {
        $arr = [3, 7, 10, 34, 73, 50];
        $dest = ArrayMethods::from($arr)->reduce(fn($carry, $item) => $carry + $item);
        $this->assertEquals(177, $dest);

        $dest2 = ArrayMethods::from($arr)->reduce(function ($carry, $item) {
            if (($item % 2) === 0) {
                $carry['even'] += $item;
            } else {
                $carry['odd'] += $item;
            }
            return $carry;
        }, ['even' => 0, 'odd' => 0]);
        $this->assertEquals(['even' => 94, 'odd' => 83], $dest2->get());
    }

    public function testForEach()
    {
        $arr = [3, 7, 10, 34, 73, 50];
        ob_start();
        ArrayMethods::from($arr)->forEach(function ($item) {
            echo ($item + 1) . ",";
        });
        $result = ob_get_clean();
        $this->assertEquals("4,8,11,35,74,51,", $result);
    }

    public function testSome()
    {
        $arr = [3, 7, 10, 34, 73, 50];
        $this->assertTrue(ArrayMethods::from($arr)->some(fn($x) => $x % 2 === 1));
        $this->assertFalse(ArrayMethods::from($arr)->some(fn($x) => $x > 80));
    }

    public function testEvery()
    {
        $arr = [3, 7, 10, 34, 73, 50];
        $this->assertTrue(ArrayMethods::from($arr)->every(fn($x) => $x <= 80));
        $this->assertFalse(ArrayMethods::from($arr)->every(fn($x) => $x % 2 === 1));
    }

    public function testFind()
    {
        $arr = [3, 7, 10, 34, 73, 50];
        $even = ArrayMethods::from($arr)->find(fn($x) => $x % 2 === 0);
        $this->assertEquals(10, $even);
    }

    public function testFindKey()
    {
        $arr = [3, 7, 10, 34, 73, 50];
        $evenKey = ArrayMethods::from($arr)->findKey(fn($x) => $x % 2 === 0);
        $this->assertEquals(2, $evenKey);
    }

    public function testSlice()
    {
        $arr = [3, 7, 10, 34, 73, 50];
        $dest = ArrayMethods::from($arr)->slice(2);
        $this->assertEquals([10, 34, 73, 50], $dest->get());

        $dest2 = ArrayMethods::from($arr)->slice(2, 3);
        $this->assertEquals([10, 34, 73], $dest2->get());
    }

    public function testSplice()
    {
        $arr = [3, 7, 10, 34, 73, 50];
        $src = ArrayMethods::from($arr);
        $dest = $src->splice(2, 2, 123, 456, 789);
        $this->assertEquals([3, 7, 123, 456, 789, 73, 50], $src->get());
        $this->assertEquals([10, 34], $dest->get());
    }

    public function testMerge()
    {
        $arr = [3, 7, 10, 34, 'k' => 73, 50];
        $src = ArrayMethods::from($arr);
        $dest = $src->merge(['q' => 201, 200]);
        $this->assertEquals([3, 7, 10, 34, 'k' => 73, 50, 'q' => 201, 200], $dest->get());

        $arr2 = [301, 'x' => 308];
        $dest2 = $src->merge(ArrayMethods::from($arr2));
        $this->assertEquals([3, 7, 10, 34, 'k' => 73, 50, 301, 'x' => 308], $dest2->get());
    }

    public function testChunk()
    {
        $arr = [1, 2, 3, 4, 5];
        $src = ArrayMethods::from($arr);
        $dest = $src->chunk(2);
        $this->assertEquals([[1, 2], [3, 4], [5]], $dest->get());
    }

    public function testColumn()
    {
        $src = ArrayMethods::from([
            ['id' => 10, 'name' => 'Alice', 'age' => 30],
            ['id' => 20, 'name' => 'Bob',   'age' => 25],
        ]);

        $this->assertEquals(['Alice', 'Bob'], $src->column('name')->get());
        $this->assertEquals([10 => 'Alice', 20 => 'Bob'], $src->column('name', 'id')->get());
        $this->assertEquals([10 => ['id' => 10, 'name' => 'Alice', 'age' => 30], 20 => ['id' => 20, 'name' => 'Bob',   'age' => 25]], $src->column(null, 'id')->get());
    }

    public function testCombine()
    {
        $keys = ['id', 'name', 'age'];
        $vals = [10, 'Alice', 30];
        $dest = ArrayMethods::combine($keys, $vals);
        $this->assertEquals(['id' => 10, 'name' => 'Alice', 'age' => 30], $dest->get());
    }

    public function testCountValues()
    {
        $vals = ArrayMethods::from(['apple', 'banana', 'apple', 'orange', 'banana'])
            ->countValues();
        $this->assertEquals(['apple' => 2, 'banana' => 2, 'orange' => 1], $vals->get());
        $nums = ArrayMethods::from([1, 2, 2, 3])
            ->countValues();
        $this->assertEquals(['1' => 1, '2' => 2, '3' => 1], $nums->get());
    }
}
