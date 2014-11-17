<?php
namespace Berthe;

use \DateTime;
use \DateTimeZone;

abstract class AbstractBuilder implements Builder {

    /**
     * @var the timezone to which the date will be transformed
     */ 
    private $timeZone = 'Europe/Paris';

    /**
     * Set the timezone
     * 
     * @param the time zone to which the date will be transformed.
     */
    public function setTimeZone($timeZone) {
        $this->timeZone = $timeZone;
    }

    /**
     * Transform a date string into a DateTime object using GMT timezone
     *
     * @param $dateString a date string that can be used as a parameter for the DateTime constructor
     *
     * @return \DateTime
     */
    protected function dateToTimeZone($stringDate) {
        $date = new DateTime($stringDate);
        $date->setTimezone(new DateTimeZone($this->timeZone));
        return $date;
    }
    
    public function updateFromArray($object, array $data = array()) {
        return $object;
    }
}