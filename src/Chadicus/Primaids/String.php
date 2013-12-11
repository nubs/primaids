<?php
namespace Chadicus\Primaids;

/**
 * Static utility class for working with the PHP string primitive type.
 */
class String
{
    /**
     * Returns true if the given string is null or contains only whitespace.
     *
     * @param string $string The string value to check.
     *
     * @return boolean
     *
     * @throws \InvalidArgumentException Thrown if the given $string is not null or a string.
     */
    final public static function isEmpty($string)
    {
        if ($string === null) {
            return true;
        }

        if (is_string($string)) {
            return trim($string) == '';
        }

        throw new \InvalidArgumentException('$string was not null or a string');
    }
}
