<?php

namespace Iwan07\ArrayMethods\base;

abstract class BaseArray implements \ArrayAccess, \Iterator, \Countable
{
    protected array $array = [];
    protected ?int $position = null;

    public function __construct(array $array = [])
    {
        $this->array = [];
        foreach ($array as $key => $value) {
            $this->array[$key] = is_array($value) ? new static($value) : $value;
        }
    }

    public static function from(array $array): self
    {
        return new static($array);
    }

    public function get(): array
    {
        $result = [];
        foreach ($this->array as $key => $item) {
            $result[$key] = $item instanceof self ? $item->get() : $item;
        }
        return $result;
    }

    // ArrayAccess
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->array);
    }

    public function offsetGet($offset)
    {
        $result = $this->array[$offset] ?? null;
        return is_array($result) ? new static($result) : $result;
    }

    public function offsetSet($offset, $value): void
    {
        if (is_array($value)) {
            $value = new static($value);
        }
        if (is_null($offset)) {
            $this->array[] = $value;
        } else {
            $this->array[$offset] = $value;
        }
    }

    public function offsetUnset($offset): void
    {
        unset($this->array[$offset]);
    }

    // Iterator
    public function current()
    {
        return $this->array[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        next($this->array);
        $this->position = key($this->array);
        return $this->position;
    }

    public function rewind()
    {
        reset($this->array);
        $this->position = key($this->array);
    }

    public function valid()
    {
        return array_key_exists($this->position, $this->array);
    }

    // Countable
    public function count(): int
    {
        return count($this->array);
    }
}
