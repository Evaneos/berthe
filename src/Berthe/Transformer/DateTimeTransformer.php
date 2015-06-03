<?php

namespace Berthe\Transformer;

class DateTimeTransformer implements TransformerInterface
{
    /**
     * @var string
     */
    protected $format;

    /**
     * @param string $format
     */
    public function __construct($format = 'Y-m-d H:i:s')
    {
        $this->format = $format;
    }

    /**
     * @param \DateTime $value
     *
     * @return string
     */
    public function transform($value)
    {
        return $value->format($this->format);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function supports($value)
    {
        return $value instanceof \DateTime || $value instanceof \DateTimeInterface;
    }
}
