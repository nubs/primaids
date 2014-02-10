<?php
namespace Chadicus\Primaids;

/**
 * Static utility class for working with the PHP array primitive type.
 */
class Arrays
{
    /**
     * Converts the given array into a string using the provided format.
     *
     * Example:
     * <pre>
     * use Chadicus\Primaids\Arrays;
     * $array = ['oranges' => .69, 'bananas' => .79, 'apples' => .89];
     * echo Util::format($array, "Fruit: {key} only {value} per pound\n");
     * </pre>
     * <br />
     * Output:
     * <pre>
     * Fruit: oranges .69 per pound
     * Fruit: bananas .79 per pound
     * Fruit: apples .89 per pound
     * </pre>
     *
     * @param array  $array            The array to be formatted.
     * @param string $format           The format template string.
     * @param string $keyPlaceHolder   The string the represents the place-holder within the format for the array key
     *                                 values.
     * @param string $valuePlaceHolder The string the represents the place-holder within the format for the array
     *                                 values.
     *
     * @return string The formatted string.
     *
     * @throws \InvalidArgumentException Thrown if $format is not a non-empty string.
     * @throws \InvalidArgumentException Thrown if $keyPlaceHolder is not a non-empty string.
     * @throws \InvalidArgumentException Thrown if $valuePlaceHolder is not a non-empty string.
     */
    final public static function format(array $array, $format, $keyPlaceHolder = '{key}', $valuePlaceHolder = '{value}')
    {
        if (!is_string($format) || trim($format) == '') {
            throw new \InvalidArgumentException('$format must be a non-empty string');
        }

        if (!is_string($keyPlaceHolder) || trim($keyPlaceHolder) == '') {
            throw new \InvalidArgumentException('$keyPlaceHolder must be a non-empty string');
        }

        if (!is_string($valuePlaceHolder) || trim($valuePlaceHolder) == '') {
            throw new \InvalidArgumentException('$valuePlaceHolder must be a non-empty string');
        }

        $result = '';
        foreach ($array as $key => $value) {
            $result .= str_replace([$keyPlaceHolder, $valuePlaceHolder], [$key, $value], $format);
        }

        return $result;
    }

    /**
     * Unsets and returns the value at index $key in the given array.
     *
     * Example:
     * <br />
     * <pre>
     * use Chadicus\Primaids\Arrays;
     * $array = ['a', 'b', 'c'];
     * $result = Arrays::getAndUnset($array, 1);
     * var_dump($result);
     * var_dump($array);
     * </pre>
     * <br />
     * Output:
     * <pre>
     * string(1) "b"
     * array(2) {
     *   [0] =>
     *   string(1) "a"
     *   [2] =>
     *   string(1) "c"
     * }
     * </pre>
     *
     * @param array          &$array The array that contains a value at index $key.
     * @param integer|string $key    The key in $array to unset and value to be returned.
     *
     * @return mixed
     */
    final public static function getAndUnset(array &$array, $key)
    {
        if (!array_key_exists($key, $array)) {
            return null;
        }

        $result = $array[$key];
        unset($array[$key]);
        return $result;
    }

    /**
     * Gets the value at index $key and passes it as a parameter to $callable returning the result of the callable call.
     *
     * Example:
     * <br />
     * <pre>
     * use Chadicus\Primaids\Arrays;
     * $array = ['a', 'b', 'c'];
     * $result = Arrays::getAndCall($array, 1, 'strtoupper');
     * var_dump($result);
     * </pre>
     * <br />
     * Output:
     * <pre>
     * string(1) "B"
     * </pre>
     *
     * @param array          $array    The array to search.
     * @param string|integer $key      The index of the string value.
     * @param callable       $callable The user function to call with the array value.
     *
     * @return mixed The return value of the callable.
     *
     * @throws \InvalidArgumentException Thrown if $key is not a string or integer.
     * @throws \OutOfBoundsException Thrown if $key is not an index of $array.
     */
    final public static function getAndCall(array $array, $key, callable $callable)
    {
        if (!is_string($key) && !is_int($key)) {
            throw new \InvalidArgumentException('$key must be a string or integer');
        }

        if (!array_key_exists($key, $array)) {
            throw new \OutOfBoundsException("'{$key}' was not a valid key");
        }

        return call_user_func_array($callable, [$array[$key]]);
    }

    /**
     * Traverses the given $array using the key path specified by $delimitedKey and returns the final value.
     *
     * Example:
     * <br />
     * <pre>
     * use Chadicus\Primaids\Arrays;
     * $array = [
     *     'db' => [
     *         'host' => 'localhost',
     *         'login' => [
     *             'username' => 'scott',
     *             'password' => 'tiger',
     *         ],
     *     ],
     * ];
     * echo Arrays::getNested($array, 'db.login.username');
     * </pre>
     * <br />
     * Output:
     * <pre>
     * scott
     * </pre>
     *
     * @param array  $array        The array to traverse.
     * @param string $delimitedKey A string of keys to traverse into the array.
     * @param string $delimiter    A string specifiying how the keys are delimited. The default is '.'.
     *
     * @return mixed The value a the inner most key or null if a key does not exist.
     *
     * @throws \InvalidArgumentException Thrown if $delimitedKey is not a non-empty string.
     * @throws \InvalidArgumentException Thrown if $delimiter is not a non-empty string.
     */
    final public static function getNested(array $array, $delimitedKey, $delimiter = '.')
    {
        if (!is_string($delimitedKey) || trim($delimitedKey) == '') {
            throw new \InvalidArgumentException('$delimitedKey must be a non-empty string');
        }

        if (!is_string($delimiter) || trim($delimiter) == '') {
            throw new \InvalidArgumentException('$delimiter must be a non-empty string');
        }

        $pointer = $array;
        foreach (explode($delimiter, $delimitedKey) as $key) {
            if (!is_array($pointer) || !array_key_exists($key, $pointer)) {
                return null;
            }

            $pointer = $pointer[$key];
        }

        return $pointer;
    }

    /**
     * Move the element at index $sourceKey to index $destinationKey.
     *
     * Example:
     * <pre>
     * use Chadicus\Primaids\Arrays;
     * $array = ['foo' => 'bar'];
     * Arrays::rename($array, 'foo', 'goo');
     * var_dump($array);
     * </pre>
     * Output:
     * <pre>
     * array(1) {
     *   'goo' =>
     *   string(3) "bar"
     * }
     * </pre>
     *
     * @param array  &$array         The array that contains a value at index $sourceKey.
     * @param string $sourceKey      The index of the source value.
     * @param string $destinationKey The new index name.
     *
     * @return void
     *
     * @throws \InvalidArgumentException Thrown if $sourceKey is not a string or integer.
     * @throws \InvalidArgumentException Thrown if $destinationKey is not a string or integer.
     * @throws \OutOfBoundsException Thrown if $sourceKey is not an index of $array.
     */
    final public static function rename(array &$array, $sourceKey, $destinationKey)
    {
        if (!is_string($sourceKey) && !is_int($sourceKey)) {
            throw new \InvalidArgumentException('$sourceKey must be a string or integer');
        }

        if (!is_string($destinationKey) && !is_int($destinationKey)) {
            throw new \InvalidArgumentException('$destinationKey must be a string or integer');
        }

        if (!array_key_exists($sourceKey, $array)) {
            throw new \OutOfBoundsException("'{$sourceKey}' was not a valid key");
        }

        $array[$destinationKey] = $array[$sourceKey];
        unset($array[$sourceKey]);
    }

    /**
     * Adds $value to $array at index $key if the given $expression is equivalent to true.
     *
     * Example:
     * <pre>
     * use Chadicus\Primaids\Arrays;
     * $array = [];
     * $value = 'a value';
     * Arrays::setIfTrue($array, 0, $value, $value !== null);
     * var_dump($array);
     * </pre>
     * <br />
     * Output:
     * <pre>
     * array(1) {
     *   [0] =>
     *   string(7) "a value"
     * }
     * </pre>
     *
     * @param array          &$array     The array to add the value.
     * @param string|integer $key        The index at which the value will be set.
     * @param mixed          $value      The value to be added.
     * @param mixed          $expression The expression to evaluate.
     *
     * @return void
     *
     * @throws \InvalidArgumentException Thrown if $key is not a string or integer.
     */
    final public static function setIfTrue(array &$array, $key, $value, $expression)
    {
        if (!is_string($key) && !is_int($key)) {
            throw new \InvalidArgumentException('$key must be a string or integer');
        }

        if ($expression) {
            $array[$key] = $value;
        }
    }

    /**
     * Groups the values within the $input array by the value within $key.
     *
     * Example:
     * <pre>
     * use Chadicus\Primaids\Arrays;
     *
     * $input = [
     *   [
     *     'name' => 'Sam',
     *     'gender' => 'M',
     *     'age' => 'Over 35',
     *   ],
     *   [
     *     'name' => 'Linda',
     *     'gender' => 'F',
     *     'age' => '25 - 35',
     *   ],
     *   [
     *     'name' => 'Max',
     *     'gender' => 'M',
     *     'age' => 'Under 25',
     *   ],
     *   [
     *     'name' => 'Phillip',
     *     'gender' => 'M',
     *     'age' => '25 - 35',
     *   ],
     * ];
     * $grouped = Arrays::groupBy($input, 'age');
     * var_dump($grouped);
     * </pre>
     * <br />
     * Output:
     * <pre>
     * array(3) {
     *   'Over 35' =>
     *    array(1) {
     *      [0] =>
     *      array(2) {
     *        'name' =>
     *        string(3) "Sam"
     *        'gender' =>
     *        string(1) "M"
     *      }
     *    }
     *    '25 - 35' =>
     *    array(2) {
     *      [0] =>
     *      array(2) {
     *        'name' =>
     *        string(1) "Linda"
     *        'gender' =>
     *        string(1) "F"
     *      }
     *      [1] =>
     *      array(2) {
     *        'name' =>
     *        string(1) "Phillip"
     *        'gender' =>
     *        string(1) "M"
     *      }
     *    }
     *    'Under 25' =>
     *    array(1) {
     *      [0] =>
     *      array(2) {
     *        'name' =>
     *        string(1) "Max"
     *        'gender' =>
     *        string(1) "M"
     *      }
     *    }
     *  }
     * </pre>
     *
     * @param array          $input The array to be grouped.
     * @param string|integer $key   The key by which the sub arrays will be grouped.
     *
     * @return array
     */
    final public static function groupBy(array $input, $key)
    {
        $result = [];
        foreach ($input as $array) {
            $keyValue = self::getAndUnset($array, $key);
            if (!array_key_exists($keyValue, $result)) {
                $result[$keyValue] = [];
            }

            $result[$keyValue][] = $array;
        }

        return $result;
    }
}
