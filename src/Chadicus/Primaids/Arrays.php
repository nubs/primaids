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
     */
    final public static function format(array $array, $format, $keyPlaceHolder = '{key}', $valuePlaceHolder = '{value}')
    {
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
     * @param boolean        $strict   Flag to throw Exception if the given $key is not found in $array.
     *
     * @return mixed The return value of the callable.
     *
     * @throws \OutOfBoundsException Thrown if $key is not an index of $array.
     */
    final public static function getAndCall(array $array, $key, callable $callable, $strict = false)
    {
        if (array_key_exists($key, $array)) {
            return call_user_func_array($callable, [$array[$key]]);
        }

        if ((bool)$strict) {
            throw new \OutOfBoundsException("Key '{$key}' was not found in input array");
        }

        return null;
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
     * @param array   $array        The array to traverse.
     * @param string  $delimitedKey A string of keys to traverse into the array.
     * @param string  $delimiter    A string specifiying how the keys are delimited. The default is '.'.
     * @param boolean $strict       Flag to throw Exception if any part of the given $delimitedKey is not found in $array.
     *
     * @return mixed The value a the inner most key or null if a key does not exist.
     *
     * @throws \OutOfBoundsException Thrown if $key is not an index of $array.
     */
    final public static function getNested(array $array, $delimitedKey, $delimiter = '.', $strict = false)
    {

        $pointer = $array;
        foreach (explode($delimiter, $delimitedKey) as $key) {
            if (is_array($pointer) && array_key_exists($key, $pointer)) {
                $pointer = $pointer[$key];
                continue;
            }

            if ((bool)$strict) {
                throw new \OutOfBoundsException("Key '{$key}' was not found in input array");
            }

            return null;
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
     * @param array   &$array         The array that contains a value at index $sourceKey.
     * @param string  $sourceKey      The index of the source value.
     * @param string  $destinationKey The new index name.
     * @param boolean $strict         Flag to throw Exception if the given $sourceKey is not found in $array.
     *
     * @return void
     *
     * @throws \OutOfBoundsException Thrown if $sourceKey is not an index of $array.
     */
    final public static function rename(array &$array, $sourceKey, $destinationKey, $strict = false)
    {
        if (array_key_exists($sourceKey, $array)) {
            $array[$destinationKey] = $array[$sourceKey];
            unset($array[$sourceKey]);
            return;
        }

        if ((bool)$strict) {
            throw new \OutOfBoundsException("Key '{$sourceKey}' was not found in input array");
        }
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
     * @param boolean        $expression The expression to evaluate.
     *
     * @return void
     */
    final public static function setIfTrue(array &$array, $key, $value, $expression)
    {
        if ((bool)$expression) {
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

    /**
     * Return an array containing a subset of the given $input array.
     *
     * Example:
     * <pre>
     * use Chadicus\Primaids\Arrays;
     * $full = [
     *     'author' => 'Gambardella, Matthew',
     *     'title' => 'XML Developer\'s Guide',
     *     'genre' => 'Computer',
     *     'price' => 44.95,
     *     'publishDate' => 970372800,
     *     'description' => 'An in-depth look at creating applications with XML.',
     *     'id' => 'bk101',
     *     'url' => '/books/bk101',
     * ];
     * $minimal = Arrays::subSet($full, ['url', 'title', 'price']);
     * var_dump($minimal);
     * </pre>
     * <br />
     * Output:
     * <pre>
     * array(3) {
     *   'url' =>
     *   string(12) "/books/bk101"
     *   'title' =>
     *   string(21) "XML Developer's Guide"
     *   'price' =>
     *   double(44.95)
     * }
     * </pre>
     *
     * @param array   $input  The array to select from.
     * @param array   $keys   The keys to select from input.
     * @param boolean $strict Flag to throw Exception if the given $keys are not found in $input.
     *
     * @return array
     *
     * @throws \OutOfBoundsException Thrown if a given key is not found in $input and $strict is true.
     */
    final public static function subSet(array $input, array $keys, $strict = false)
    {
        $result = [];
        foreach ($keys as $i => $key) {
            if (array_key_exists($key, $input)) {
                $result[$key] = $input[$key];
                continue;
            }

            if ((bool)$strict) {
                throw new \OutOfBoundsException("Key '{$key}' was not found in input array");
            }
        }

        return $result;
    }
}
