<?php

namespace DataLinx\PhpUtils;

class ArrayHelper
{
    /**
     * Recursively flattens an array.
     *
     * array(array(1, 2), array(3))
     *
     * Becomes:
     *
     * array(1, 2, 3)
     *
     * @param mixed $array
     * @param array $target Optional target array
     * @return array|null
     */
    public static function flatten($array, array &$target = []): ?array
    {
        if (is_array($array)) {
            foreach ($array as $piece) {
                self::flatten($piece, $target);
            }
        } else {
            $target[] = $array;
        }

        return $target;
    }
}
