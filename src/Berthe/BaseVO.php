<?php
namespace Berthe;

class BaseVO {
    protected $version = 1;
    protected $id = 0;

    public function setVersion($version) {
        $this->version = $version;
        return $this;
    }

    public function getVersion() {
        return $this->version;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getId() {
        return $this->id;
    }

    /**
     * Constructor
     * @param array $infos An array of infos from database or form
     */
    public function __construct(array $properties = array()) {
        $this->setProperties($properties);
        $this->calcProperties();
    }

    protected function setProperties($properties) {
        foreach($properties as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
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