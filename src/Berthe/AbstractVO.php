<?php
namespace Berthe;

abstract class AbstractVO {
    public $version = 1;

    /**
     * Constructor
     * @param array $infos An array of infos from database or form
     */
    public function __construct() {
        $this->calcProperties();
    }

    /**
     * Computes the properties of the VO if needed
     */
    protected function calcProperties() {
        $this->version = static::VERSION;
        return true;
    }

    /**
     * @return array
     */
    public function __toArray() {
        return get_object_vars($this);
    }
}