<?php

namespace Berthe\Transformer;

interface TransformerResolverInterface
{
    /**
     * @param mixed $value
     *
     * @return TransformerInterface
     */
    public function resolve($value);
}
