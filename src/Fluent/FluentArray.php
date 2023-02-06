<?php

namespace DataLinx\PhpUtils\Fluent;

class FluentArray
{
    /**
     * @var array Subject array
     */
    protected array $array;

    /**
     * @var mixed
     */
    private $before;

    /**
     * @var mixed
     */
    private $after;

    private bool $beforeAfterKey = false;

    private bool $strict = true;

    /**
     * Create a new FluentArray object
     *
     * @param array $array
     */
    public function __construct(array $array)
    {
        $this->array = $array;
    }

    /**
     * @return array
     */
    public function getArray(): array
    {
        return $this->array;
    }

    /**
     * @param array $array
     * @return FluentArray
     */
    public function setArray(array $array): FluentArray
    {
        $this->array = $array;

        return $this;
    }

    /**
     * Recursively flattens an array.
     *
     * array(array(1, 2), array(3))
     *
     * Becomes:
     *
     * array(1, 2, 3)
     *
     * @param array $target Optional target array
     * @return $this
     */
    public function flatten(array &$target = []): self
    {
        $this->array = self::flattenRecursive($this->array, $target);

        return $this;
    }

    /**
     * Helper function for the flatten method
     *
     * @param array $array Source array
     * @param array $target Target array
     * @return array
     * @noinspection PhpMissingParamTypeInspection
     */
    private static function flattenRecursive($array, array &$target = []): ?array
    {
        if (is_array($array)) {
            foreach ($array as $piece) {
                self::flattenRecursive($piece, $target);
            }
        } else {
            $target[] = $array;
        }

        return $target;
    }

    public function __toString()
    {
        return (string)print_r($this->getArray(), true);
    }

    public function before($element, bool $strict = true): self
    {
        $this->resetBeforeAfter();

        $this->before = $element;
        $this->beforeAfterKey = false;
        $this->strict = $strict;

        return $this;
    }

    public function beforeKey($key, bool $strict = true): self
    {
        $this->resetBeforeAfter();

        $this->before = $key;
        $this->beforeAfterKey = true;
        $this->strict = $strict;

        return $this;
    }

    public function after($element, bool $strict = true): self
    {
        $this->resetBeforeAfter();

        $this->after = $element;
        $this->beforeAfterKey = false;
        $this->strict = $strict;

        return $this;
    }

    public function afterKey($key, bool $strict = true): self
    {
        $this->resetBeforeAfter();

        $this->after = $key;
        $this->beforeAfterKey = true;
        $this->strict = $strict;

        return $this;
    }

    private function resetBeforeAfter(): self
    {
        $this->before = null;
        $this->after = null;
        $this->beforeAfterKey = false;
        $this->strict = true;

        return $this;
    }

    /**
     * Insert element to array
     *
     * @param mixed $value Value to insert
     * @param string|int|null $key Optional literal key to use for insertion
     * @return $this
     */
    public function insert($value, $key = null): self
    {
        if ($this->before !== null) {
            $insert = [
                $key ?? 0 => $value,
            ];

            if ($this->beforeAfterKey) {
                $index = $this->positionOfKey($this->before, $this->strict);
            } else {
                $index = $this->positionOf($this->before, $this->strict);
            }

            if ($index === 1) {
                // Prepend to the beginning of the array
                $this->array = array_merge($insert, $this->array);
            } elseif ($index !== null) {
                // Slice and merge
                $index--;
                $this->array = array_merge(array_slice($this->array, 0, $index, true), $insert, array_slice($this->array, $index, null, true));
            }
        } elseif ($this->after !== null) {
            $insert = [
                $key ?? 0 => $value,
            ];

            if ($this->beforeAfterKey) {
                $index = $this->positionOfKey($this->after, $this->strict);
            } else {
                $index = $this->positionOf($this->after, $this->strict);
            }

            if ($index === count($this->array)) {
                // Append to the end of the array
                $this->array = array_merge($this->array, $insert);
            } elseif ($index !== null) {
                // Slice and merge
                $this->array = array_merge(array_slice($this->array, 0, $index, true), $insert, array_slice($this->array, $index, null, true));
            }
        } elseif ($key) {
            // Append at the end with specified key
            $this->array[$key] = $value;
        } else {
            // Append at the end
            $this->array[] = $value;
        }

        $this->resetBeforeAfter();

        return $this;
    }

    /**
     * Get sequential position of the value in the array, if it exists. The first element is at the position of 1.
     *
     * @param mixed $value Value to find
     * @param bool $strict Use strict comparison (type and value)
     * @return int|null Position or null if not found
     */
    public function positionOf($value, bool $strict = true): ?int
    {
        $pos = 1;

        foreach ($this->array as $element) {
            if ($strict) {
                if ($element === $value) {
                    return $pos;
                }
            }
            /** @noinspection TypeUnsafeComparisonInspection */
            elseif ($element == $value) {
                return $pos;
            }
            $pos++;
        }

        return null;
    }

    /**
     * Get sequential position of the key in the array, if it exists. The first element is at the position of 1.
     *
     * @param string|int $key Key to find
     * @param bool $strict Use strict comparison (type and value)
     * @return int|null Position or null if not found
     */
    public function positionOfKey($key, bool $strict = true): ?int
    {
        $pos = 1;

        foreach (array_keys($this->array) as $e_key) {
            if ($strict) {
                if ($e_key === $key) {
                    return $pos;
                }
            }
            /** @noinspection TypeUnsafeComparisonInspection */
            elseif ($e_key == $key) {
                return $pos;
            }
            $pos++;
        }

        return null;
    }

    /**
     * Remove element by value. Removes all occurrences of the value in the array.
     *
     * @param mixed $value Value(s) to remove (primitive type or array)
     * @param bool $strict Use strict comparison (type and value)
     * @return $this
     */
    public function remove($value, bool $strict = true): self
    {
        $this->array = array_filter($this->array, static function ($element) use ($value, $strict) {
            if (is_array($value)) {
                return ! in_array($element, $value, $strict);
            }

            /** @noinspection TypeUnsafeComparisonInspection */
            return $strict ? ($element !== $value) : ($element != $value);
        });

        return $this;
    }
}
