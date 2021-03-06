<?php
namespace Aura\View\Helper;

/**
 * Test class for Base.
 * Generated by PHPUnit on 2011-04-02 at 08:28:30.
 */
class BaseTest extends \PHPUnit_Framework_TestCase
{
    public function test__invoke()
    {
        $base = new Base;
        $href = '/path/to/base';
        $actual = $base($href);
        $expect = '    <base href="/path/to/base" />' . PHP_EOL;
        $this->assertSame($expect, $actual);
    }
}
