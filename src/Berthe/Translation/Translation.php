<?php

namespace Berthe\Translation;

class Translation
{
    protected $id = 0;
    protected $translations = array();

    /**
     * @return string default_language_iso2
     */
    protected $default_language_iso2;

    /**
     * @return string default_language_iso2
     */
    public function getDefaultLanguageIso2()
    {
        return $this->default_language_iso2;
    }

    /**
     * @param string $value
     * @return Translation
     */
    public function setDefaultLanguageIso2($value)
    {
        $this->default_language_iso2 = $value;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setTranslations(array $translations = array())
    {
        $this->translations = $translations;
        return $this;
    }

    public function getTranslations()
    {
        return $this->translations;
    }


    public function getTranslation($iso2)
    {
        return (array_key_exists($iso2, $this->translations) ? $this->translations[$iso2] : null);
    }

    public function format($iso2 = null, $param = null)
    {
        if (!$iso2 && $this->getDefaultLanguageIso2()) {
            $iso2 = $this->getDefaultLanguageIso2();
        }

        if (!$iso2) {
            return '';
        }

        if (array_key_exists($iso2, $this->translations)) {
            switch($param) {
                case 'content' :
                    return $this->translations[$iso2]->getContent();
                case 'name' :
                default :
                    return $this->translations[$iso2]->getName();
            }
        }

        return '';
    }

    public function addTranslation(TranslationRow $translation)
    {
        $this->translations[$translation->getIso2()] = $translation;
        return $this;
    }

    public function __toString()
    {
        return $this->format($this->getDefaultLanguageIso2());
    }

    public function __toArray()
    {
        $toArray = get_object_vars($this);
        $translations = $toArray['translations'];
        $toArray['translations'] = array();
        foreach($translations as $key => $t) {
            $toArray['translations'][$key] = $t->__toArray();
        }

        return $toArray;
    }
}