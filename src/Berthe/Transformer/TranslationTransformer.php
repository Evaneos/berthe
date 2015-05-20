<?php

namespace Berthe\Transformer;

use Berthe\Translation\Translation;

class TranslationTransformer implements TransformerInterface
{
    /**
     * @param Translation $value
     *
     * @return string
     */
    public function transform($value)
    {
        return $value->getId();
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function supports($value)
    {
        return $value instanceof Translation;
    }
}
