<?php

namespace App\Core;
abstract class KuriakosData implements \ArrayAccess
{

    public array $types = [];
    public array $component_times = [];

    final public function offsetSet($offset, $value): void
    {
        if (is_string($offset)) {
            $this->$offset = $value;
        }
    }

    final public function offsetUnset(mixed $offset): void
    {
        if (is_string($offset)) {
            unset($this->$offset);
        }
    }

    final public function offsetGet($offset): mixed
    {
        return (is_string($offset) && isset($this->$offset)) ? $this->$offset : null;
    }
}