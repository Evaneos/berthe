<?php
namespace Berthe;

abstract class AbstractVO implements VO
{
    protected $version = 1;
    protected $id = 0;

    public function getTranslatableFields()
    {
        return array();
    }

    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Constructor
     * @param array $infos An array of infos from database or form
     */
    public function __construct(array $properties = array())
    {
        $this->setProperties($properties);
        $this->calcProperties();
    }

    protected function setProperties($properties)
    {
        foreach ($properties as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * Computes the properties of the VO if needed
     */
    protected function calcProperties()
    {
        $this->version = static::VERSION;
        return true;
    }

    /**
     * @return array
     */
    public function __toArray()
    {
        $toArray = get_object_vars($this);
        $translatable = $this->getTranslatableFields();
        foreach ($translatable as $field) {
            if (array_key_exists($field, $toArray) && !is_null($toArray[$field])) {
                $toArray[$field] = $toArray[$field]->__toArray();
            }
        }

        return $toArray;
    }
}