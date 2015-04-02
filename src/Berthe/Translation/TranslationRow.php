<?php

namespace Berthe\Translation;

class TranslationRow
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $content;

    /** @var string */
    protected $iso2;

    /** @var int */
    protected $language_id;

    /**
     * @param $name
     * @param $content
     * @param $languageId
     * @param $iso2
     */
    public function __construct($name, $content, $languageId, $iso2)
    {
        $this->name = $name;
        $this->content = $content;
        $this->language_id = $languageId;
        $this->iso2 = $iso2;
    }

    /**
     * @return string name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $value
     * @return self
     */
    public function setName($value)
    {
        $this->name = $value;
        return $this;
    }

    /**
     * @return string content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $value
     * @return self
     */
    public function setContent($value)
    {
        $this->content = $value;
        return $this;
    }

    /**
     * @return string iso2
     */
    public function getIso2()
    {
        return $this->iso2;
    }

    /**
     * @param string $value
     * @return self
     */
    public function setIso2($value)
    {
        $this->iso2 = $value;
        return $this;
    }

    /**
     * @return int language_id
     */
    public function getLanguageId()
    {
        return $this->language_id;
    }

    /**
     * @param int $value
     * @return self
     */
    public function setLanguageId($value)
    {
        $this->language_id = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function __toArray()
    {
        return get_object_vars($this);
    }
}
