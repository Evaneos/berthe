<?php

namespace Berthe\Test\Fixture\My;

use Berthe\AbstractVO;

class VO extends AbstractVO
{
    const VERSION = 1;

    /**
     * @var \Datetime
     */
    protected $created_at = null;


    public function getDatetimeFields()
    {
        return array('created_at');
    }

    /**
     * @return \Datetime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }
}
