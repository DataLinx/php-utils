<?php

namespace DataLinx\PhpUtils\Fluent;

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
}
