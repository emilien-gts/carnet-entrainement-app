<?php

namespace App\Core;

class Utils
{
    /**
     * @return string[]
     */
    public static function day_of_weeks(): array
    {
        return ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    }

    public static function are_arrays_equal(array ...$arrays): bool
    {
        foreach ($arrays as &$array) {
            sort($array);
        }

        $firstArray = array_shift($arrays);
        foreach ($arrays as $a) {
            if ($firstArray !== $a) {
                return false;
            }
        }

        return true;
    }
}
