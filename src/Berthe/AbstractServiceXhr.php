<?php
abstract class Evaneos_Berthe_AbstractServiceXhr {
    /**
     * @var Berthe_FactoryService
     */
    public $serviceFactory = null;
    /**
     * @var Berthe_Context
     */
    public $context = null;
    /**
     * Determines if $methodName method is actually accessible by user
     * @param string $methodName The of the method to test
     * @return boolean 
     */
    public function isMethodAccessible($methodName){
        return true;
    }
    
    protected function getResult($success = false, $message = "", $data = null) {
	return array('success' => $success, 'message' => $message, 'data' => $data);
    }
    
    /**
     * @param array $data
     */
    public function update(array $data = array()) {
        if (!array_key_exists('id', $data)) {
            throw new RuntimeException('Invalid data (missing ID)');
        }
        $id = $data['id'];
        
        $isAllOk = true;
        foreach($data as $key => $value) {
            if (strtolower($key) == 'id') {
                continue;
            }
            $res = $this->chain((int)$id, $key, $value);
            if (!$res) {
                $isAllOk = false;
            }
        }
        
        $_vo = $this->_getupdatedVo($id);
        
        $_message = '';
        if($isAllOk) {
            $_message = t('La modification a été effectuée avec succès');
        } else {
            $_message = t('Une erreur est survenue');
        }
        
        return $this->getResult($isAllOk, $_message, array('vo' => $_vo));
    }
    
    /**
     * METHOD TO OVERRIDE
     * @param int $id
     * @param string $key
     * @param mixed $value
     * @return boolean
     */
    protected function chain($id, $key, $value) {
        return true;
    }
    
    /**
     * May (or may not) return the updated vo by its Id
     * @param integer $id
     * @return Evaneos_Berthe_AbstractVO
     */
    protected function _getupdatedVo($id) {
        return null;
    }
}