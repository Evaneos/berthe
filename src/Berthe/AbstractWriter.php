<?php

/**
 * Class definition for Writer abstract Evaneos_Berthe_Writer
 * 
 * @author anthony@evaneos.com
 * @copyright Evaneos
 * @version 1.0 
 * @filesource Evaneos/Berthe/Writer.php
 * @package Evaneos/Berthe
 * @since Berthe
 */
abstract class Evaneos_Berthe_AbstractWriter {
    /**
     * @var Berthe_Context
     */
    public $context = null;
    
    /**
     *
     * @var Evaneos_Berthe_DbWriter
     */
    protected $_db = null;
    
    public function __construct(Berthe_Context $context = null) {
        $this->_db = Zend_Registry::get('dbWriter');
        $this->context = $context;
    }
    
    /**
     * Insert the object in database
     * @param Evaneos_Berthe_AbstractVO $object the object to insert
     * @return boolean 
     */
    abstract public function insert(Evaneos_Berthe_AbstractVO $object);
    /**
     * Update the object in database
     * @param Evaneos_Berthe_AbstractVO $object the object to insert
     * @return boolean 
     */
    abstract public function update(Evaneos_Berthe_AbstractVO $object);
    /**
     * Delete the object from database
     * @param Evaneos_Berthe_AbstractVO $object the object to insert
     * @return boolean 
     */
    abstract public function delete(Evaneos_Berthe_AbstractVO $object);
    /**
     * Delete an object by id from database
     * @param int $int object identifier
     * @return boolean 
     */
    abstract public function deleteById($id);
}