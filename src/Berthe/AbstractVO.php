<?php
namespace Berthe;

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
                    $this->setDatetimeValue($key, $value);
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

    /**
     * @param string $key property name
     * @param \Datetime | string $value
     * @throws \Exception ? TODO
     * @return void
     */
    protected function setDatetimeValue($key, $value)
    {
        if (null === $value || $value instanceof \DateTime) {
            $this->{$key} = $value;
        } else {
            try {
                if (!is_string($value)) {
                    throw new \InvalidArgumentException(sprintf('Invalid type specified for field "%s" of class "%s". Expected string|Datetime, got %s', $key, get_class($this), gettype($value)));
                }
                if (strlen($value) > 19) {
                    $value = substr($value, 0, 19);
                }
                if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}\s[0-9]{2}:[0-9]{2}:[0-9]{2}$/', $value)) {
                    throw new \InvalidArgumentException(sprintf('Invalid datetime format specified for field "%s" of class "%s". Expected "Y-m-d H:i:s", got "%s"', $key, get_class($this), $value));
                }

                $value = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
                if (!$value) {
                    throw new \InvalidArgumentException(sprintf('Unable to create a valid datetime for field "%s" of class "%s" with value "%s"', $key, get_class($this), $value));
                }
                $this->{$key} = $value;
            }
            catch (\Exception $e) {
                //TODO
                // throw $e ?
                // $this->{$key} = null; ?
            }
        }
    }
}
