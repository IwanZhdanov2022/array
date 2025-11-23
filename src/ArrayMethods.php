<?php

namespace Iwan07\ArrayMethods;

use Iwan07\ArrayMethods\base\BaseArray;

class ArrayMethods extends BaseArray
{
    public static function explode(string $delimiter, string $string): self
    {
        return new static(explode($delimiter, $string));
    }

    public static function from(array $array): self
    {
        return parent::from($array);
    }

    public function clone(): self
    {
        return new static($this->get());
    }

    public function sort(?callable $callback = null): self
    {
        $sortedArray = $this->array;
        if (!is_null($callback)) {
            usort($sortedArray, $callback);
        } else {
            sort($sortedArray);
        }
        return new static($sortedArray);
    }

    public function ksort(): self
    {
        $sortedArray = $this->array;
        ksort($sortedArray);
        return new static($sortedArray);
    }

    public function map(callable $callback): self
    {
        return new static(array_map($callback, $this->array, array_keys($this->array)));
    }

    public function filter(?callable $callback = null): self
    {
        return new static(array_filter($this->array, $callback));
    }

    public function forEach(callable $callback): void
    {
        foreach ($this->array as $key => $value) {
            $callback($value, $key);
        }
    }

    public function find(callable $callback)
    {
        foreach ($this->array as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }
        return null;
    }

    public function findKey(callable $callback)
    {
        foreach ($this->array as $key => $value) {
            if ($callback($value, $key)) {
                return $key;
            }
        }
        return null;
    }

    public function every(callable $callback): bool
    {
        $tmp = array_filter($this->array, $callback);
        return count($tmp) === count($this->array);
    }

    public function some(callable $callback): bool
    {
        $tmp = array_filter($this->array, $callback);
        return !empty($tmp);
    }

    public function keys(): self
    {
        return new static(array_keys($this->array));
    }

    public function values(): self
    {
        return new static(array_values($this->array));
    }

    public function push($value): self
    {
        $this->array[] = $value;
        return $this;
    }

    public function pop()
    {
        return array_pop($this->array);
    }

    public function unshift($value): self
    {
        array_unshift($this->array, $value);
        return $this;
    }

    public function shift()
    {
        return array_shift($this->array);
    }

    public function reduce(callable $callback, $initial = null)
    {
        $result = array_reduce($this->array, $callback, $initial);
        return is_array($result) ? new static($result) : $result;
    }

    public function slice($offset, $length = null, $preserveKeys = false): self
    {
        $sliced = array_slice($this->array, $offset, $length, $preserveKeys);
        return new static($sliced);
    }

    public function splice(int $offset, ?int $length = null, ...$replacements): self
    {
        foreach ($replacements as &$item) {
            if (is_array($item)) {
                $item = new static($item);
            }
        }
        unset($item);
        $newArray = array_splice($this->array, $offset, $length, $replacements);
        return new static($newArray);
    }

    public function merge(self|array $array): self
    {
        $addArray = $array instanceof self ? $array->get() : $array;
        $merged = array_merge($this->array, $addArray);
        return new static($merged);
    }

    public function implode(string $glue = ''): string
    {
        return implode($glue, $this->array);
    }
}
