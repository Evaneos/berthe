<?php

namespace Evaneos\Berthe\DAL;

abstract class Berthe_AbstractWriter {
    /**
     * @var Berthe_Context
     */
    public $context = null;

    /**
     *
     * @var Berthe_DbWriter
     */
    protected $_db = null;

    public function __construct(Berthe_Context $context = null) {
        $this->_db = Zend_Registry::get('dbWriter');
        $this->context = $context;
    }

    /**
     * Insert the object in database
     * @param Berthe_AbstractVO $object the object to insert
     * @return boolean
     */
    abstract public function insert(Berthe_AbstractVO $object);
    /**
     * Update the object in database
     * @param Berthe_AbstractVO $object the object to insert
     * @return boolean
     */
    abstract public function update(Berthe_AbstractVO $object);
    /**
     * Delete the object from database
     * @param Berthe_AbstractVO $object the object to insert
     * @return boolean
     */
    abstract public function delete(Berthe_AbstractVO $object);
    /**
     * Delete an object by id from database
     * @param int $int object identifier
     * @return boolean
     */
    abstract public function deleteById($id);
}