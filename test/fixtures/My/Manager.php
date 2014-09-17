<?php

namespace Berthe\Test\Fixture\My;

class Manager extends \Berthe\AbstractManager {
    public function getVoForCreation() {
        return new MyVO();
    }

    public function run() {
        return $this->getById(rand(1, 4));
    }
}