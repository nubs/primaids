<?php
namespace Chadicus\Primaids;

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
     * Verify behaviour of getAndCall with missing key parameter.
     *
     * @test
     * @covers ::getAndCall
     *
     * @return void
     */
    public function getAndCallWithMissingKey()
    {
        $this->assertNull(Arrays::getAndCall(['a', 'b'], 'c', 'strtoupper'));
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
     * Verify array remains unchanged if the $sourceKey does not exist in the input array and $strict is false.
     *
     * @test
     * @covers ::rename
     *
     * @return void
     */
    public function renameWithMissingSourceKey()
    {
        $array = ['a', 'b'];
        Arrays::rename($array, 'foo', 2, false);
        $this->assertSame(['a', 'b'], $array);
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
     * Verify basic behavior of batch().
     *
     * @test
     * @covers ::batch
     *
     * @return void
     */
    public function batch()
    {
        $input = array('a', 'b', 'c', 'd', 'e');
        $actual = Arrays::batch($input, 2);
        $this->assertSame(
            array(
                array(0 => 'a', 1 => 'b', 2 => 'c'),
                array(3 => 'd', 4 => 'e'),
            ),
            $actual
        );
    }
}
