<?php
namespace My;

class Manager extends \Berthe\AbstractManager {
    public function getVoForCreation() {
        return new MyVO();
    }

    public function run() {
        return $this->getStorage()->fetchObject();
    }
}