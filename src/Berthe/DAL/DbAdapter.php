<?php

namespace Berthe\DAL;

class DbAdapter
{
    /**
     *
     * @var \Zend_Db_Adapter_Abstract
     */
    protected $db = null;

    public function __construct(\Zend_Db_Adapter_Abstract $db)
    {
        $this->db = $db;
    }

    /**
     * @return \Zend_Db_Adapter_Abstract
     */
    public function getAdapter()
    {
        return $this->db;
    }
}
