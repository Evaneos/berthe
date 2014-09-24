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
            if (strlen($value) > 19) {
                $value = substr($value, 0, 19);
            }
            if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}\s[0-9]{2}:[0-9]{2}:[0-9]{2}$/', $value)) {
                throw new \InvalidArgumentException(sprintf('Invalid datetime string format specified. Expected "Y-m-d H:i:s", got "%s"', $value));
            }

            $date = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
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
