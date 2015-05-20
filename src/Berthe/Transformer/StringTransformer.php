<?php

namespace Berthe\Transformer;

class StringTransformer implements TransformerInterface
{
    /**
     * @param $string
     *
     * @return bool
     */
    protected function checkUtf8Encoding($string)
    {
        return (bool) preg_match(
            '%^(?:
            [\x09\x0A\x0D\x20-\x7E] # ASCII
            | [\xC2-\xDF][\x80-\xBF] # non-overlong 2-byte
            | \xE0[\xA0-\xBF][\x80-\xBF] # excluding overlongs
            | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
            | \xED[\x80-\x9F][\x80-\xBF] # excluding surrogates
            | \xF0[\x90-\xBF][\x80-\xBF]{2} # planes 1-3
            | [\xF1-\xF3][\x80-\xBF]{3} # planes 4-15
            | \xF4[\x80-\x8F][\x80-\xBF]{2} # plane 16
            )*$%xs',
            $string
        );
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    public function transform($value)
    {
        if (function_exists('mb_check_encoding')) {
            $isUtf8 = mb_check_encoding($value, 'UTF-8');
        } else {
            $isUtf8 = $this->checkUtf8Encoding($value);
        }

        return  $isUtf8 ? $value : utf8_encode($value);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function supports($value)
    {
        return is_string($value);
    }
}
