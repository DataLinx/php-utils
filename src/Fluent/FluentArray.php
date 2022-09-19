<?php

namespace DataLinx\PhpUtils\Fluent;

use InvalidArgumentException;

class FluentArray
{
    /**
     * @var array Subject array
     */
    protected array $array;

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
        return print_r($this->getArray(), true);
    }

    /**
     * Insert value before the specified value
     *
     * @param mixed $before
     * @param mixed $value
     * @param string|null $key Optional key, when using assoc. array
     * @param bool $strict Use strict comparison (same type and value)
     * @return $this
     */
    public function insertBefore($before, $value, ?string $key = null, bool $strict = true): self
    {
        $new = [];
        $should_insert = null;

        foreach ($this->array as $k => $v) {
            // If we haven't inserted the element yet....
            if ($should_insert === null) {
                // ... compare the values
                if ($strict) {
                    if ($v === $before) {
                        $should_insert = true;
                    }
                } elseif ($v == $before) {
                    $should_insert = true;
                }
            }

            if ($should_insert) {
                if ($key) {
                    $new[$key] = $value;
                } else {
                    $new[] = $value;
                }

                $should_insert = false; // Block further comparisons and insertions
            }

            if (is_string($k)) {
                $new[$k] = $v;
            } else {
                $new[] = $v;
            }
        }

        $this->array = $new;

        return $this;
    }

    /**
     * Insert the value before the specified key or index
     *
     * @param mixed $before
     * @param mixed $value
     * @param string|null $key Optional key, when using assoc. array
     * @param bool $strict Use strict comparison (same type and value)
     * @return $this
     */
    public function insertBeforeKey($before, $value, ?string $key = null, bool $strict = true): self
    {
        if (! array_key_exists($before, $this->array)) {
            throw new InvalidArgumentException("The provided \"before\" key does not exist in the array!");
        }

        // inserting before specified index position
        if (is_int($before)) {
            $insert = [
                $value,
            ];

            $this->array = array_merge(array_slice($this->array, 0, $before), $insert, array_slice($this->array, $before));
        }
        // inserting before specified associative key
        elseif (is_string($before)) {
            $index = 0;
            foreach ($this->array as $k=>$element) {
                $insert = [
                    $key => $value,
                ];

                if ($strict) {
                    if ($k === $before) {
                        $this->array = array_merge(array_slice($this->array, 0, $index), $insert, array_slice($this->array, $index));
                    }
                } elseif ($k == $before) {
                    $this->array = array_merge(array_slice($this->array, 0, $index), $insert, array_slice($this->array, $index));
                }

                $index++;
            }
        }

        return $this;
    }

    /**
     * Insert the value after the specified key or index
     *
     * @param mixed $after
     * @param mixed $value
     * @param string|null $key Optional key, when using assoc. array
     * @param bool $strict Use strict comparison (same type and value)
     * @return $this
     */
    public function insertAfterKey($after, $value, ?string $key = null, bool $strict = true): self
    {
        if (! array_key_exists($after, $this->array)) {
            throw new InvalidArgumentException("The provided \"after\" key does not exist in the array!");
        }

        // inserting after specified index position
        if (is_int($after)) {
            $insert = [
                $value,
            ];

            $this->array = array_merge(array_slice($this->array, 0, $after + 1), $insert, array_slice($this->array, $after + 1));
        }
        // inserting after specified associative key
        elseif (is_string($after)) {
            $index = 0;
            foreach ($this->array as $k=>$element) {
                $insert = [
                    $key => $value,
                ];

                if ($strict) {
                    if ($k === $after) {
                        $this->array = array_merge(array_slice($this->array, 0, $index + 1), $insert, array_slice($this->array, $index + 1));
                    }
                } elseif ($k == $after) {
                    $this->array = array_merge(array_slice($this->array, 0, $index + 1), $insert, array_slice($this->array, $index + 1));
                }

                $index++;
            }
        }

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
            } elseif ($element == $value) {
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
            } elseif ($e_key == $key) {
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
        $this->array = array_filter($this->array, function ($element) use ($value, $strict) {
            if (is_array($value)) {
                return ! in_array($element, $value, $strict);
            } else {
                return $strict ? ($element !== $value) : ($element != $value);
            }
        });

        return $this;
    }
}
