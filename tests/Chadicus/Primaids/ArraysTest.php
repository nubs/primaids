<?php
namespace Chadicus\Primaids;

use Chadicus\Primaids\Arrays;

/**
 * Unit tests for \Chadicus\Primaids\Arrays class.
 *
 * @coversDefaultClass \Chadicus\Primaids\Arrays
 */
final class ArraysTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Verify basic behavior of the format method.
     *
     * @test
     * @covers ::format
     *
     * @return void
     */
    public function format()
    {
        $array = ['Sam' => 34, 'John' => 28, 'Anne' => 30];
        $format = "Name: {key} Age: {value}\n";
        $expected = "Name: Sam Age: 34\nName: John Age: 28\nName: Anne Age: 30\n";
        $this->assertSame($expected, Arrays::format($array, $format));
    }

    /**
     * Verifies behaviour when format is called with an invalid $format parameter.
     *
     * @param mixed $format The invalid format.
     *
     * @test
     * @covers ::format
     * @dataProvider badFormats
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $format must be a non-empty string
     *
     * @return void
     */
    public function formatWithInvalidFormat($format)
    {
        Arrays::format([], $format);
    }

    /**
     * Verifies behaviour when format is called with an invalid $keyPlaceHolder parameter.
     *
     * @param mixed $keyPlaceHolder The invalid key place-holder.
     *
     * @test
     * @covers ::format
     * @dataProvider badKeyPlaceHolders
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $keyPlaceHolder must be a non-empty string
     *
     * @return void
     */
    public function formatWithInvalidKeyPlaceHolder($keyPlaceHolder)
    {
        Arrays::format([], 'not under test', $keyPlaceHolder);
    }

    /**
     * Verifies behaviour when format is called with an invalid $valuePlaceHolder parameter.
     *
     * @param mixed $valuePlaceHolder The invalid value place-holder.
     *
     * @test
     * @covers ::format
     * @dataProvider badValuePlaceHolders
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $valuePlaceHolder must be a non-empty string
     *
     * @return void
     */
    public function formatWithInvalidValuePlaceHolder($valuePlaceHolder)
    {
        Arrays::format([], 'not under test', 'not under tests', $valuePlaceHolder);
    }

    /**
     * Verifies basic behavior of the getAndUnset method.
     *
     * @test
     * @covers ::getAndUnset
     *
     * @return void
     */
    public function getAndUnset()
    {
        $array = ['a', 'b', 'c'];
        $this->assertSame('b', Arrays::getAndUnset($array, 1));
        $this->assertSame([0 => 'a', 2 => 'c'], $array);
    }

    /**
     * Verifies behavior of getAndUnset when the given key does not exist in the given array.
     *
     * @test
     * @covers ::getAndUnset
     *
     * @return void
     */
    public function getAndUnsetWithKeyNotFound()
    {
        $array = ['a', 'b', 'c'];
        $this->assertNull(Arrays::getAndUnset($array, 3));
        $this->assertSame(['a', 'b', 'c'], $array);
    }

    /**
     * Verify basic behavior of getAndCall.
     *
     * @test
     * @covers ::getAndCall
     *
     * @return void
     */
    public function getAndCall()
    {
        $this->assertSame(
            'Hello, Chadicus',
            Arrays::getAndCall(
                ['Turtle', 'Chadicus'],
                1,
                function ($name) {
                    return "Hello, {$name}";
                }
            )
        );
    }

    /**
     * Verify behaviour of getAndCall with invalid key parameter.
     *
     * @test
     * @covers ::getAndCall
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $key must be a string or integer
     *
     * @return void
     */
    public function getAndCallWithInvalidKey()
    {
        Arrays::getAndCall(['a', 'b'], true, 'strtoupper');
    }

    /**
     * Verify behaviour of getAndCall with key parameter that does not exist in the array.
     *
     * @test
     * @covers ::getAndCall
     * @expectedException \OutOfBoundsException
     * @expectedExceptionMessage 'foo' was not a valid key
     *
     * @return void
     */
    public function getAndCallWithNonExistentKey()
    {
        Arrays::getAndCall(['a', 'b'], 'foo', 'strtoupper');
    }

    /**
     * Data provider method for the formatWithInvalidFormat.
     *
     * @return array
     */
    public function badFormats()
    {
        return [
            'emptyString' => [''],
            'null' => [null],
            'nonString' => [1],
        ];
    }

    /**
     * Data provider method for the formatWithInvalidKeyPlaceholder.
     *
     * @return array
     */
    public function badKeyPlaceholders()
    {
        return [
            'emptyString' => [''],
            'null' => [null],
            'nonString' => [1],
        ];
    }

    /**
     * Data provider method for the formatWithInvalidValuePlaceholder.
     *
     * @return array
     */
    public function badValuePlaceHolders()
    {
        return [
            'emptyString' => [''],
            'null' => [null],
            'nonString' => [1],
        ];
    }

    /**
     * Verify basic functionality of getNested.
     *
     * @test
     * @covers ::getNested
     *
     * @return void
     */
    public function getNested()
    {
        $array = ['db' => ['host' => 'localhost', 'login' => [ 'username' => 'scott', 'password' => 'tiger']]];
        $this->assertSame('scott', Arrays::getNested($array, 'db.login.username'));
    }

    /**
     * Verify behavior when invalid $delimitedKey values are given to getNested.
     *
     * @param mixed $delimitedKey The invalid delimitedKey.
     *
     * @test
     * @covers ::getNested
     * @dataProvider badDelimitedKeys
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $delimitedKey must be a non-empty string
     *
     * @return void
     */
    public function getNestedWithInvalidDelimitedKey($delimitedKey)
    {
        Arrays::getNested([], $delimitedKey);
    }

    /**
     * Data provider method for getNestedWithInvalidDelimitedKey.
     *
     * @return array
     */
    public function badDelimitedKeys()
    {
        return [
            'emptyString' => [''],
            'null' => [null],
            'nonString' => [1],
        ];
    }

    /**
     * Verify behavior when invalid $delimiter values are given to getNested.
     *
     * @param mixed $delimiter The invalid delimiter.
     *
     * @test
     * @covers ::getNested
     * @dataProvider badDelimiters
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $delimiter must be a non-empty string
     *
     * @return void
     */
    public function getNestedWithInvalidDelimiter($delimiter)
    {
        Arrays::getNested([], 'not.under.test', $delimiter);
    }

    /**
     * Data provider method for getNestedWithInvalidDelimiter.
     *
     * @return array
     */
    public function badDelimiters()
    {
        return [
            'emptyString' => [''],
            'null' => [null],
            'nonString' => [1],
        ];
    }

    /**
     * Verify behavior when the given delimitedKey does not exist in the given array.
     *
     * @test
     * @covers ::getNested
     *
     * @return void
     */
    public function getNestedPathNotFound()
    {
        $array = ['db' => ['host' => 'localhost', 'login' => [ 'username' => 'scott', 'password' => 'tiger']]];
        $this->assertNull(Arrays::getNested($array, 'db.notfound.username'));
    }

    /**
     * Verify basic behavior of rename().
     *
     * @test
     * @covers ::rename
     *
     * @return void
     */
    public function rename()
    {
        $array = ['a', 'b'];
        Arrays::rename($array, 0, 2);
        $this->assertSame([1 => 'b', 2 => 'a'], $array);
    }

    /**
     * Verify behavior when an invalid $sourceKey is given to rename().
     *
     * @test
     * @covers ::rename
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $sourceKey must be a string or integer
     *
     * @return void
     */
    public function renameWithInvalidSourceKey()
    {
        $array = ['a', 'b'];
        Arrays::rename($array, false, 2);
    }

    /**
     * Verify behavior when an invalid $destinationKey is given to rename().
     *
     * @test
     * @covers ::rename
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $destinationKey must be a string or integer
     *
     * @return void
     */
    public function renameWithInvalidDestinationKey()
    {
        $array = ['a', 'b'];
        Arrays::rename($array, 0, false);
    }

    /**
     * Verify behaviour of rename with $sourceKey parameter that does not exist in the array.
     *
     * @test
     * @covers ::rename
     * @expectedException \OutOfBoundsException
     * @expectedExceptionMessage 'foo' was not a valid key
     *
     * @return void
     */
    public function renameWithMissingSourceKey()
    {
        $array = ['a', 'b'];
        Arrays::rename($array, 'foo', 2);
    }

    /**
     * Verify basic behavior of setIfTrue.
     *
     * @test
     * @covers ::setIfTrue
     *
     * @return void
     */
    public function setIfTrue()
    {
        $a = ['a'];
        Arrays::setIfTrue($a, 1, 'b', true);
        $this->assertSame(['a', 'b'], $a);
        Arrays::setIfTrue($a, 2, 'c', false);
        $this->assertSame(['a', 'b'], $a);
    }

    /**
     * Verify behavior of setIfTrue if $key is not a string or integer.
     *
     * @test
     * @covers ::setIfTrue
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $key must be a string or integer
     *
     * @return void
     */
    public function setIfTrueWithInvalidKey()
    {
        $a = ['a'];
        Arrays::setIfTrue($a, new \StdClass(), 'b', true);
    }

    /**
     * Verify basic functionality of groupBy().
     *
     * @test
     * @covers ::groupBy
     * @uses \Chadicus\Primaids\Arrays::getAndUnset
     *
     * @return void
     */
    public function groupBy()
    {
        $input = [
            [
                'foo' => 'a',
                'bar' => 'b',
                'target' => 'x',
            ],
            [
                'foo' => 'c',
                'bar' => 'd',
                'target' => 'y',
            ],
            [
                'foo' => 'e',
                'bar' => 'f',
                'target' => 'z',
            ],
            [
                'foo' => 'g',
                'bar' => 'h',
                'target' => 'y',
            ],
        ];

        $expected = [
            'x' => [
                [
                    'foo' => 'a',
                    'bar' => 'b',
                ],
            ],
            'y' => [
                [
                    'foo' => 'c',
                    'bar' => 'd',
                ],
                [
                    'foo' => 'g',
                    'bar' => 'h',
                ],
            ],
            'z' => [
                [
                    'foo' => 'e',
                    'bar' => 'f',
                ],
            ],
        ];

        $this->assertSame($expected, Arrays::groupBy($input, 'target'));
    }

    /**
     * Verifies basic behavior of subSet().
     *
     * @test
     * @covers ::subSet
     *
     * @return void
     */
    public function subSet()
    {
        $input = ['d' => 'lemon', 'a' => 'orange', 'b' => 'banana', 'c' => 'apple'];
        $this->assertSame(['d' => 'lemon', 'c' => 'apple'], Arrays::subSet($input, ['d', 'c']));
    }

    /**
     * Verifies subSet does not throw if $strict is false and a $key is not present in $input.
     *
     * @test
     * @covers ::subSet
     *
     * @return void
     */
    public function subSetMissingKey()
    {
        $input = ['d' => 'lemon', 'a' => 'orange', 'b' => 'banana', 'c' => 'apple'];
        $this->assertSame(['d' => 'lemon'], Arrays::subSet($input, ['d', 'notThere']));
    }

    /**
     * Verify $strict param must be boolean.
     *
     * @test
     * @covers ::subSet
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $strict must be a boolean value
     *
     * @return void
     */
    public function subSetStrictNotBoolean()
    {
        Arrays::subSet([], [], 'not boolean');
    }

    /**
     * Verify $strict param must be boolean.
     *
     * @test
     * @covers ::subSet
     * @expectedException \OutOfBoundsException
     * @expectedExceptionMessage Key 'notThere' was not found in input array
     *
     * @return void
     */
    public function subSetStrictMissingKey()
    {
        $input = ['d' => 'lemon', 'a' => 'orange', 'b' => 'banana', 'c' => 'apple'];
        Arrays::subSet($input, ['a', 'notThere'], true);
    }
}
