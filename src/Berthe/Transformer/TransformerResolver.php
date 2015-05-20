<?php

namespace Berthe\Transformer;

class TransformerResolver implements TransformerResolverInterface
{
    /**
     * @var TransformerInterface[]
     */
    protected $transformers;

    public function __construct()
    {
        $this->transformers = array();
    }

    /**
     * @param TransformerInterface $transformer
     */
    public function addTransformer(TransformerInterface $transformer)
    {
        $this->transformers[] = $transformer;
    }

    /**
     * @param mixed $value
     *
     * @return TransformerInterface
     */
    public function resolve($value)
    {
        foreach ($this->transformers as $transformer) {
            if ($transformer->supports($value)) {
                return $transformer;
            }
        }

        return new NullTransformer();
    }
}
