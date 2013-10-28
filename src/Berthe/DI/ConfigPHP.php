<?php
class Berthe_DI_ConfigPHP extends Berthe_DI_ConfigAbstract {
    protected $filePath = null;

    public function __construct($filePath) {
        $this->filePath = $filePath;
    }

    public function load() {
        if (file_exists($this->filePath) && is_readable($this->filePath)) {
            include $this->filePath;
            return $array;
        }
        else {
            throw new RuntimeException("Couldn't load the array in path '" . $this->filePath . "'");
        }
    }
}