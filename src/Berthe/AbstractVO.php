<?php
namespace Berthe;

abstract class AbstractVO {
    public $version = 1;

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
    protected function calcProperties() {
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
        $this->setAttributes($infos);
        $_ret and $this->version = $this::VERSION;
        // compute attributes
        $_ret and $_ret = $this->calcProperties();
        return $_ret;
    }

    /**
     * Populates the attributes with values from $infos
     * @param array $infos
     * @return boolean
     */
    protected function setAttributes(array $infos = array()) {
        foreach (array_keys($infos) as $key) {
            $prop = null;
            if(property_exists($this, $key)) {
                $prop = $key;
            }

            if ($prop) {
                $this->setProp($prop, $infos[$key]);
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
                    }
                    elseif(is_string($value)) {
                        // instanciate the object with the string
                        $newValue = new DateTime($value);
                    }
                    else {
                        // if none, trigger an error
                        trigger_error(__CLASS__ . '::' . __FUNCTION__ . '() : Wrong Class for propery ' . $_prop, E_USER_ERROR);
                        return false;
                    }
                    break;
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