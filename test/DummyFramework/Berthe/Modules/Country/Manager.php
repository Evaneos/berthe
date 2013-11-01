<?php
class Berthe_Modules_Country_Manager {
    /**
     * @var Berthe_Store_Abstract
     */
    public $storage = null;

    public function __construct() {

    }

    protected function validate() {
        $errors = new Berthe_ErrorHandler_Errors();

        // force false test
        $parameter = true;
        $validatingStuff = $parameter == false;
        if (!$validatingStuff) {
            $error = new Berthe_ErrorHandler_Error('not validating test boolean', 100101, array($parameter));
            $errors->addError($error);
            $error = new Berthe_ErrorHandler_Error('not validating test A', 100102, array('data1' => rand(1, 10), 'data2' => rand(10, 100)));
            $errors->addError($error);
        }

        if ($errors->hasErrors()) {
            $errors->throwMe();
        }

        return true;
    }

    public function save($data) {
        $this->validate();
        return $this->storage->save($data);
    }
}