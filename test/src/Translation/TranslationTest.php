<?php

use Berthe\Translation\Translation;
use Berthe\Translation\TranslationRow;

class TranslationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_get_the_first_non_empty_translation_row()
    {
        $translation = new Translation();
        $expected = self::given_a_non_empty_translation(1, 'fr');

        $translation->addTranslation(self::given_an_empty_translation(2, 'es'));
        $translation->addTranslation(self::given_an_empty_translation(3, 'it'));
        $translation->addTranslation($expected);

        $firstNonEmptyTranslation = $translation->getFirstNonEmptyTranslation();

        $this->assertEquals($expected->getIso2(), $firstNonEmptyTranslation->getIso2());
        $this->assertEquals($expected->getName(), $firstNonEmptyTranslation->getName());
        $this->assertEquals($expected->getContent(), $firstNonEmptyTranslation->getContent());
        $this->assertEquals($expected->getLanguageId(), $firstNonEmptyTranslation->getLanguageId());
    }

    /**
     * @test
     */
    public function it_should_get_the_iso2_of_empty_translation()
    {
        $translation = new Translation();

        $translation->addTranslation(self::given_a_non_empty_translation(1, 'fr'));
        $translation->addTranslation(self::given_an_empty_translation(2, 'es'));
        $translation->addTranslation(self::given_an_empty_translation(3, 'it'));

        $iso2WithoutContent = $translation->getIso2WithoutName();

        $this->assertEquals(['es', 'it'], $iso2WithoutContent);
    }

    /**
     * @test
     */
    public function it_should_get_the_first_different_translation_row_between_two_translation()
    {
        $translationOne = new Translation();
        $translationOne->addTranslation(self::given_a_non_empty_translation(1, 'fr'));
        $translationOne->addTranslation(self::given_a_non_empty_translation(2, 'es'));

        $translationTwo = new Translation();
        $translationTwo->addTranslation(self::given_a_non_empty_translation(2, 'es'));
        $translationTwo->addTranslation(self::given_an_empty_translation(1, 'fr'));

        $firstDifferentTranslation = $translationOne->getFirstDifferentTranslation($translationTwo);

        $this->assertEquals('fr', $firstDifferentTranslation->getIso2());
        $this->assertEquals(1, $firstDifferentTranslation->getLanguageId());
        $this->assertEquals('something', $firstDifferentTranslation->getName());
        $this->assertEquals('something better', $firstDifferentTranslation->getContent());
    }

    /**
     * @test
     */
    public function it_should_format_returning_empty_string_when_iso2_is_null_and_no_default_iso2()
    {
        $translation = new Translation();
        $formated = $translation->format();

        $this->assertEquals('', $formated);
    }

    /**
     * @test
     */
    public function it_should_format_using_default_iso2_when_iso2_is_null_and_default_iso2_is_set()
    {
        $translation = new Translation();
        $translation->addTranslation(self::given_a_non_empty_translation(1, 'fr'));
        $translation->setDefaultLanguageIso2('fr');

        $formated = $translation->format();
        $this->assertEquals('something', $formated);
    }

    /**
     * @test
     */
    public function it_should_format_using_name_when_no_param_is_precised()
    {
        $translation = new Translation();
        $translation->addTranslation(self::given_a_non_empty_translation(1, 'fr'));

        $formated = $translation->format('fr');
        $this->assertEquals('something', $formated);
    }

    /**
     * @test
     */
    public function it_should_format_using_precised_param()
    {
        $translation = new Translation();
        $translation->addTranslation(self::given_a_non_empty_translation(1, 'fr'));

        $formated = $translation->format('fr', 'content');
        $this->assertEquals('something better', $formated);
    }

    /**
     * @test
     */
    public function it_should_format_only_when_iso2_has_translation()
    {
        $translation = new Translation();

        $formated = $translation->format('fr', 'content');
        $this->assertEquals('', $formated);
    }

    /**
     * @param int $id
     * @param string iso2
     * @return TranslationRow
     */
    private static function given_a_non_empty_translation($id = 1, $iso2 = 'fr')
    {
        return new TranslationRow('something', 'something better', $id, $iso2);
    }

    /**
     * @param int $id
     * @param string iso2
     * @return TranslationRow
     */
    private static function given_an_empty_translation($id = 2, $iso2 = 'es')
    {
        return new TranslationRow(null, null, $id, $iso2);
    }
}
