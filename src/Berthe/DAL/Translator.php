<?php

namespace Berthe\DAL;

interface Translator
{
    /**
     * @param \Berthe\DAL\DbReader $db
     * @return Translator
     */
    public function setDb(\Berthe\DAL\DbReader $db);

    /**
     * @param  array  $ids
     * @return \Berthe\Translation\Translation[]
     */
    public function getTranslations(array $ids = array());

    /**
     * @param  array  $translations
     * @return boolean
     */
    public function saveTranslation(\Berthe\Translation\Translation $translation);
}
