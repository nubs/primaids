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
     * <br />
     * <code>
     * use Chadicus\Primaids\Arrays;
     * $array = ['oranges' => .69, 'bananas' => .79, 'apples' => .89];
     * echo Util::format($array, "Fruit: {key} only {value} per pound\n");
     * </code>
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
     * <code>
     * use Chadicus\Primaids\Arrays;
     * $array = ['a', 'b', 'c'];
     * $result = Arrays::getAndUnset($array, 1);
     * var_dump($result);
     * var_dump($array);
     * </code>
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
     * <code>
     * use Chadicus\Primaids\Arrays;
     * $array = ['a', 'b', 'c'];
     * $result = Arrays::getAndCall($array, 1, 'strtoupper');
     * var_dump($result);
     * </code>
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
}
