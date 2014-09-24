<?php

use Berthe\Test\Fixture\My\VO;

class AbstractVOTest extends PHPUnit_Framework_TestCase
{
    public function testHydratationOfValidDatetimeField()
    {
        $vo = new VO(array('created_at' => '2014-12-25 10:00:00'));
        $this->assertTrue($vo->getCreatedAt() instanceof \DateTime);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testHydratationOfInValidDatetimeField()
    {
        new VO(array('created_at' => 'fake'));
    }
}
