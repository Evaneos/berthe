<?php

/**
 * Class definition for VO abstract Evaneos_Berthe_VO
 * 
 * @author anthony@evaneos.com
 * @copyright Evaneos
 * @version 1.0 
 * @filesource Evaneos/Berthe/VO.php
 * @package Evaneos/Berthe
 * @since Berthe
 */
abstract class Evaneos_Berthe_AbstractVO {
    public $version = 1;
    /**
     * Used to match the sql fields with the VO attributes
     * @example
     *  $_propMapper = array(
     *      'sql_field'       => 'voField',       // sql field as key
     *      'other_sql_field' => 'otherVoField',
     *      'foo'             => 'bar'
     *  );
     * @var array
     */
    protected $_propMapper = array();
    /**
     * Constructor
     * @param array $infos An array of infos from database or form
     */
    public function __construct(array $infos = array()) {
        $this->populate($infos);
    }
    /**
     * Computes the properties of the VO if needed
     */
    protected function _calcProperties() {
        return true;
    }
    
    /**
     * populates the VO's attributes with values from $infos array and computed
     * the needed ones
     * @param array $infos 
     * @return boolean
     */
    public function populate(array $infos = array()) {
        // set attributes
        $_ret           = $this->_setAttributes($infos);
        $_ret and $this->version = $this::VERSION;
        // compute attributes
        $_ret and $_ret = $this->_calcProperties();
        return $_ret;
    }
    
    /**
     * Populates the attributes with values from $infos
     * @param array $infos 
     * @return boolean
     */
    protected function _setAttributes(array $infos = array()) {
        // for each attribute
        foreach (array_keys($infos) as $key) {
            
            // if !exists
            if(!property_exists($this, $key)) {
                // if not in prop mapper
                if(!isset($this->_propMapper[$key])) {
                    // trigger an exception
                    //trigger_error(__CLASS__ . '::' . __FUNCTION__ . '() : Property ' . $key . ' does not exists.', E_USER_NOTICE);
                    // continue
                    continue;
                // if in propr mapper save the sql prop name
                } else {
                    $_prop = $this->_propMapper[$key];
                }
            // else
            } else {
                // save the prop name
                $_prop = $key;
            }
            if(!$this->_setProp($_prop, $infos[$key])) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Sets a props, casting the value
     * IF THE DEFAULT VALUE IS NULL THERE IS NO CASTING
     * @param string $prop
     * @param mixed $value
     * @return boolean 
     */
    protected function _setProp($prop, $value) {
            // cast the type and set it
            $_currentValue = $this->{$prop};
            //switch type
            switch (true) {
                // bool
                case is_bool($_currentValue) :
                    $newValue = (boolean)$value;
                    break;
                // int
                case is_int($_currentValue) :
                    $newValue = (int)$value;
                    break;
                // float
                case is_float($_currentValue) :
                    $newValue = (float)$value;
                    break;
                case ($_currentValue instanceof DateTime) :
                    if($value instanceof DateTime) {
                        $newValue = $value;
                    // if not check if it is a string
                    } elseif(is_string($value)) {
                        // instanciate the object with the string
                        $newValue = new DateTime($value);
                    } else {
                        // if none, trigger an error
                        trigger_error(__CLASS__ . '::' . __FUNCTION__ . '() : Wrong Class for propery ' . $_prop, E_USER_ERROR);
                        return false;
                    }
                    break;
                // object DEPRECATED
                /*
                case is_object($_currentValue) :
                    // get class of current value in VO
                    $_class = get_class($_currentValue);
                    // check new value is an object of same class
                    if(is_object($value) and $_class == get_class($value)) {
                        $newValue = $value;
                    // if not check if it is a string
                    } elseif(is_string($value)) {
                        // instanciate the object with the string
                        $newValue = new $_class($value);
                    } else {
                        // if none, trigger an error
                        trigger_error(__CLASS__ . '::' . __FUNCTION__ . '() : Wrong Class for propery ' . $_prop, E_USER_ERROR);
                        return false;
                    }
                    break;
                 */
                // string, null and others
                case is_string($_currentValue) :
                case is_null($_currentValue) && is_string($value) :
                default :
                    $newValue = $value;
                    if (is_scalar($value)) {
                        $newValue = mb_check_encoding($value, 'UTF-8') ? $value : utf8_encode($value);
                    }
                    break;
            }
            //set
            $this->{$prop} = $newValue;
            return true;
    }
    
    /**
     * @return array
     */
    public function __toArray() {
        return get_object_vars($this);
    }
}