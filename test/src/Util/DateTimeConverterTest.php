<?php

use Berthe\Util\DateTimeConverter;

class DateTimeConverterTest extends PHPUnit_Framework_TestCase
{
    public function providerInvalidFormats()
    {
        return array (
            array('2014-10-12'),
            array('toto'),
            array(1.0),
            array('1.0'),
            array(new \stdClass()),
            array(549865464984654984654984654654987654),
            array('2014-10-10T20:20:14.123+0100'),
            array('2014-10-10T20:20:14,123+0100'),
        );
    }

    public function providerValidFormats()
    {
        return array (
            array(new \DateTime()),
            array('2014-10-10 20:20:14'),
            array('2014-10-10 20:20:14.150'),
            array('2014-10-10T20:20:14+0100'),
            array('2014-10-10T20:20:14-0200'),
            array(10000),
            array('10000'),
        );
    }

    /**
     * @dataProvider providerValidFormats
     */
    public function testHydratationWithGoodDatetimeArgument($data)
    {
        $date = DateTimeConverter::convert($data);
        $this->assertTrue($date instanceof \DateTime);
    }

    /**
     * @dataProvider providerInvalidFormats
     * @expectedException \InvalidArgumentException
     */
    public function testHydratationWithBadDatetimeArgument($data)
    {
        DateTimeConverter::convert($data);
    }

    public function testHydratationWithNullDatetimeArgument()
    {
        $this->assertNull(DateTimeConverter::convert(null));
    }
}
