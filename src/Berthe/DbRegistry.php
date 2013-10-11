<?php

/**
 * Class definition for DbRegistry
 * 
 * @author anthony@evaneos.com
 * @copyright Evaneos
 * @version 1.0 
 * @filesource Evaneos/Berthe/DbRegistry.php
 * @package Evaneos/Berthe
 * @since Berthe
 */
class Evaneos_Berthe_DbRegistry {
    /**
     *
     * @var Evaneos_Berthe_DbReader
     */
    private static $_reader = null;
    /**
     * 
     * @var Evaneos_Berthe_DbWriter
     */
    private static $_writer = null;
    /**
     * Returns the reader adapter
     * @return Evaneos_Berthe_DbReader
     */
    public static function getReader() {
        if(is_null(self::$_reader)) {
            throw new Exception(__CLASS__ . '::' . __FUNCTION__ . ' : No reader adapter set.');
        }
        return self::$_reader;
    }
    /**
     * Returns the writer adapter
     * @return Evaneos_Berthe_DbWriter
     */
    public static function getWriter() {
        if(is_null(self::$_writer)) {
            throw new Exception(__CLASS__ . '::' . __FUNCTION__ . ' : No writer adapter set.');
        }
        return self::$_writer;
    }
    /**
     * Sets the reader adapter
     * @param Evaneos_Berthe_DbReader $reader 
     */
    public static function setReader(Evaneos_Berthe_DbReader $reader) {
        self::$_reader = $reader;
    }
    /**
     * Sets the writer adapter
     * @param Evaneos_Berthe_DbWriter $writer
     */
    public static function setWriter(Evaneos_Berthe_DbWriter $writer) {
        self::$_writer = $writer;
    }
}