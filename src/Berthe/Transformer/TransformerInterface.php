<?php

namespace Berthe\Transformer;

interface TransformerInterface
{
    /**
     * @param mixed $value
     *
     * @return string
     */
    public function transform($value);

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function supports($value);
}
