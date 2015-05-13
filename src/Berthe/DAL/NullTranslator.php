<?php

namespace Berthe\DAL;

class NullTranslator implements Translator
{
    /**
     * @param \Berthe\DAL\DbReader $db
     * @return Translator
     */
    public function setDb(\Berthe\DAL\DbReader $db)
    {
    }

    /**
     * @param  array  $ids
     * @return \Berthe\Translation\Translation[]
     */
    public function getTranslations(array $ids = array())
    {
        $output = array();
        foreach ($ids as $id) {
            $t = new \Berthe\Translation\Translation();
            $t->setId($id);
            $output[] = $t;
        }

        return $output;
    }

    /**
     * @param  array  $translations
     * @return boolean
     */
    public function saveTranslation(\Berthe\Translation\Translation $translation)
    {
        return true;
    }
}
