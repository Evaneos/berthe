<?php

namespace Berthe\Transformer;

class NullTransformer implements TransformerInterface
{
    /**
     * @param mixed $value
     *
     * @return string
     */
    public function transform($value)
    {
        return $value;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function supports($value)
    {
        return true;
    }
}
