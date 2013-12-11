<?php
namespace Chadicus\Primaids;

use Chadicus\Primaids\String;

/**
 * Unit tests for \Chadicus\Array\String class.
 */
final class StringTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Verify basic behavior of isEmpty.
     *
     * @test
     * @covers \Chadicus\Primaids\String::isEmpty
     *
     * @return void
     */
    public function isEmptyBasicUse()
    {
        $this->assertTrue(String::isEmpty(null));
        $this->assertTrue(String::isEmpty(''));
        $this->assertTrue(String::isEmpty("\t\n "));
        $this->assertFalse(String::isEmpty('a'));
    }

    /**
     * Verify behavior when an invalid $string is given to isEmpty().
     *
     * @test
     * @covers \Chadicus\Primaids\String::isEmpty
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $string was not null or a string
     *
     * @return void
     */
    public function isEmptyWithNonString()
    {
        String::isEmpty(1);
    }
}
