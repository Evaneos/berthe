<?php

namespace Berthe\Transformer;

class BooleanTransformer implements TransformerInterface
{
    /**
     * @param bool $value
     *
     * @return string
     */
    public function transform($value)
    {
        return (int) $value;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function supports($value)
    {
        return is_bool($value);
    }
}
