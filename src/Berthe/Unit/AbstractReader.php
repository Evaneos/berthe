<?php


abstract class Berthe_Unit_AbstractReader extends Evaneos_Berthe_AbstractReader {
    
    abstract function getTableName();
    
    protected function _getRandomIds() {
        $_nbResults = rand(0,20);
        $_ids = array();
        for($i = 0; $i < $_nbResults; $i++) {
            $_ids[] = $this->_getData()->generateRandomObject()->id;
        }
        return $_ids;
    }

    /**
     * @return Berthe_Unit_AbstractData 
     */
    abstract protected function _getData();
    
    // Methods
    /**
     * Loads the Vo's datas $ids ids and return an array of raw datas
     * @param array $ids The ids to load
     * @return array The datas as array
     */
    public function selectByIds(array $ids = array()) {
        $_data = $this->_getData();
        $_res  = array();
        foreach ($ids as $_id) {
            $_res[$id] = $_data[$id];
        }
        return $_res;
    }
    
    /**
     * Returns random ids, simulating a filtering
     * @param Berthe_Modules_Transaction_Filter $filter
     * @return array 
     */
    public function getIdsByFilter(Berthe_Modules_Transaction_Filter $filter) {
        return $this->_getRandomIds();
    }
    
    /**
     * Returns random ids, simulating a pagination
     * @param Evaneos_Berthe_Paginator $paginator
     * @param Berthe_Modules_Transaction_Filter $filter
     * @return array
     */
    public function getIdsPaginatedByFilter(Evaneos_Berthe_Paginator $paginator, Berthe_Modules_Transaction_Filter $filter) {
        $_nbResults = $paginator->getNbByPage();
        $_ids = array();
        for($i = 0; $i < $_nbResults; $i++) {
            $_ids[] = $this->_getData()->generateRandomObject()->id;
        }
        return $_ids;
    }
    
    /**
     * Returns a random count
     * @param Berthe_Modules_Transaction_Filter $filter
     * @return array
     */
    public function getCountByFilter(Berthe_Modules_Transaction_Filter $filter) {
        return rand(0,20);
    }
}