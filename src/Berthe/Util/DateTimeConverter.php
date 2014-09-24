<?php

namespace Berthe\Util;

final class DateTimeConverter
{

    /**
     * @param mixed $value
     * @return \DateTime|null
     * @throws \InvalidArgumentException
     */
    public static function convert($value)
    {
        $v = null;

        if (null === $value || $value instanceof \DateTime) {
            return $value;
        }

        if (!$v) { $v = self::intConverter($value); }
        if (!$v) { $v = self::stringConverter($value); }
        if (!$v) {
            throw new \InvalidArgumentException(sprintf('Invalid type specified for datetime conversion : "%s"', gettype($value)));
        }
        else {
            return $v;
        }
    }

    private static function stringConverter($value)
    {
        if (is_string($value)) {

            if (preg_match('/^\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}(\.\d{3})?$/', $value)) {
                $date = \DateTime::createFromFormat('Y-m-d H:i:s', substr($value, 0, 19));
            } elseif (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}[+-]\d{4}$/', $value)) {
                $date = \DateTime::createFromFormat(\DateTime::ISO8601, $value);
            } else {
                throw new \InvalidArgumentException(sprintf('Invalid datetime string format specified. Expected "%s" or "%s", got "%s"', 'yyyy-mm-dd hh:mm:ss[.iii]', 'yyyy-mm-ddThh:mm:ss[-+]pppp', $value));
            }

            if (!$date) {
                throw new \InvalidArgumentException(sprintf('Invalid string to datetime conversion with value "%s"', $value));
            }

            return $date;
        }
        return null;
    }

    private static function intConverter($value)
    {
        if (is_string($value) && $value === (string)intval($value)) {
            $value = (int)$value;
        }

        if (is_int($value)) {
            $date = new \DateTime();
            $date->setTimestamp($value);
            if (!$date) {
                throw new \InvalidArgumentException(sprintf('Invalid int to datetime conversion with value "%s"', $value));
            }
            return $date;
        }

        return null;
    }
}
