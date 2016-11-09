<?php

namespace Berthe\Translation;

class Translation
{
    /** @var int */
    protected $id = 0;

    /** @var TranslationRow[] */
    protected $translations = array();

    /** @var string */
    protected $default_language_iso2;

    /**
     * @return string
     */
    public function getDefaultLanguageIso2()
    {
        return $this->default_language_iso2;
    }

    /**
     * @param string $value
     * @return self
     */
    public function setDefaultLanguageIso2($value)
    {
        $this->default_language_iso2 = $value;
        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param TranslationRow[] $translations
     * @return $this
     */
    public function setTranslations(array $translations = array())
    {
        $this->translations = $translations;
        return $this;
    }

    /**
     * @return TranslationRow[]
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * @param string $iso2
     * @return TranslationRow|null
     */
    public function getTranslation($iso2)
    {
        return isset($this->translations[$iso2]) ? $this->translations[$iso2] : null;
    }

    /**
     * @param string $iso2
     * @param string $param content|name
     * @return string
     */
    public function format($iso2 = null, $param = null)
    {
        if (!$iso2 && $this->getDefaultLanguageIso2()) {
            $iso2 = $this->getDefaultLanguageIso2();
        }

        if (!$iso2) {
            return '';
        }

        if (array_key_exists($iso2, $this->translations)) {
            switch ($param) {
                case 'content':
                    return $this->translations[$iso2]->getContent();
                case 'name':
                default:
                    return $this->translations[$iso2]->getName();
            }
        }

        return '';
    }

    /**
     * @param TranslationRow $translation
     * @return $this
     */
    public function addTranslation(TranslationRow $translation)
    {
        $this->translations[$translation->getIso2()] = $translation;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getIso2WithoutName()
    {
        return array_keys(
            array_filter(
                $this->getTranslations(),
                function ($translation) {
                    return empty($translation->getName());
                }
            )
        );
    }

    /**
     * @return TranslationRow|null
     */
    public function getFirstNonEmptyTranslation()
    {
        foreach ($this->getTranslations() as $translation) {
            if (!empty($translation->getName())) {
                return $translation;
            }
        }

        return null;
    }

    /**
     * @param Translation $otherTranslation
     * @return TranslationRow|null
     */
    public function getFirstDifferentTranslation(Translation $otherTranslation) {
        /** @var TranslationRow $translation */
        foreach ($this->getTranslations() as $translation) {
            $oldTranslation = $otherTranslation->getTranslation($translation->getIso2());
            if ($oldTranslation === null ||
                $translation->getName() !== $oldTranslation->getName()
            ) {
                return $translation;
            }
        }

        return null;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->format($this->getDefaultLanguageIso2());
    }

    /**
     * @return array
     */
    public function __toArray()
    {
        $toArray = get_object_vars($this);
        $toArray['translations'] = array();
        foreach ($this->translations as $key => $t) {
            $toArray['translations'][$key] = $t->__toArray();
        }
        return $toArray;
    }
}
