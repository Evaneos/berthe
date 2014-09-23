<?php

use Berthe\Test\Fixture\My\VO;

class AbstractVOTest extends PHPUnit_Framework_TestCase
{

    public function providerExceptions()
    {
        return array (
            array('2014-10-12'),
            array('toto'),
            array(1.0),
            array(new \stdClass()),
            array(549865464984654984654984654654987654),
        );
    }

    public function providerTrue()
    {
        return array (
            array(new \DateTime()),
            array('2014-10-10 20:20:14'),
            array('2014-10-10 20:20:14.150'),
            array(10000),
        );
    }

    /**
     * @dataProvider providerTrue
     */
    public function testHydratationWithGoodDatetimeArgument($data)
    {
        $vo = new VO(array('created_at' => $data));
        $this->assertTrue($vo->getCreatedAt() instanceof \DateTime);
    }

    /**
     * @dataProvider providerExceptions
     * @expectedException \InvalidArgumentException
     */
    public function testHydratationWithBadDatetimeArgument($data)
    {
        new VO(array('created_at' => $data));
    }

    public function testHydratationWithNullDatetimeArgument()
    {
        $vo = new VO(array('created_at' => null));
        $this->assertTrue(null === $vo->getCreatedAt());
    }
}
