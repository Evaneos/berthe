<?php
namespace Berthe;

use Berthe\Util\DateTimeConverter;

abstract class AbstractVO implements VO
{
    protected $version = 1;
    protected $id = 0;

    /**
     * @inheritdoc
     */
    public function getTranslatableFields()
    {
        return array();
    }

    /**
     * @inheritdoc
     */
    public function getDatetimeFields()
    {
        return array();
    }

    /**
     * Return asked attribute
     *
     * @param string $name attribute name
     * @return mixed
     */
    public function getAttribute($name)
    {
        if (!isset($this->$name)) {
            return null;
        }

        return $this->$name;
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
        $this->version = static::VERSION;
        $this->setProperties($properties);
        $this->calcProperties();
    }

    protected function setProperties($properties)
    {
        foreach ($properties as $key => $value) {
            if (property_exists($this, $key)) {
                if (in_array($key, $this->getDatetimeFields())) {
                    $this->{$key} = DateTimeConverter::convert($value);
                } else {
                    $this->{$key} = $value;
                }
            }
        }
    }

    /**
     * Computes the properties of the VO if needed
     */
    protected function calcProperties()
    {
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
