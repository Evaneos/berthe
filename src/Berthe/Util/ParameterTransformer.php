<?php

namespace Berthe\Util;

use Berthe\Transformer\TransformerResolverInterface;

class ParameterTransformer
{
    /**
     * @var TransformerResolverInterface
     */
    protected $transformerResolver;

    /**
     * @param TransformerResolverInterface $transformerResolver
     */
    public function __construct(TransformerResolverInterface $transformerResolver)
    {
        $this->transformerResolver = $transformerResolver;
    }

    /**
     * @param array $parameters
     *
     * @return string[]
     */
    public function transform(array $parameters)
    {
        $transformed = array();

        foreach ($parameters as $name => $value) {
            $transformed[$name] = $this->transformerResolver->resolve($value)->transform($value);
        }

        return $transformed;
    }
}
