<?php

/**
 * Class definition for DbRegistry
 * 
 * @author dev@evaneos.com
 * @copyright Evaneos
 * @version 1.0 
 * @filesource Berthe/DbRegistry.php
 * @package Berthe
 */
class Berthe_DbRegistry {
    /**
     *
     * @var Berthe_DbReader
     */
    private static $_reader = null;
    /**
     * 
     * @var Berthe_DbWriter
     */
    private static $_writer = null;
    /**
     * Returns the reader adapter
     * @return Berthe_DbReader
     */
    public static function getReader() {
        if(is_null(self::$_reader)) {
            throw new Exception(__CLASS__ . '::' . __FUNCTION__ . ' : No reader adapter set.');
        }
        return self::$_reader;
    }
    /**
     * Returns the writer adapter
     * @return Berthe_DbWriter
     */
    public static function getWriter() {
        if(is_null(self::$_writer)) {
            throw new Exception(__CLASS__ . '::' . __FUNCTION__ . ' : No writer adapter set.');
        }
        return self::$_writer;
    }
    /**
     * Sets the reader adapter
     * @param Berthe_DbReader $reader 
     */
    public static function setReader(Berthe_DbReader $reader) {
        self::$_reader = $reader;
    }
    /**
     * Sets the writer adapter
     * @param Berthe_DbWriter $writer
     */
    public static function setWriter(Berthe_DbWriter $writer) {
        self::$_writer = $writer;
    }
}