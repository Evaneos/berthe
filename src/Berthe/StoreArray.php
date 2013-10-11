<?php
class Evaneos_Berthe_StoreArray extends Evaneos_Berthe_AbstractStore {
    protected $objects = array();
    
    protected function _load(array $ids = array()) {
        $output = array();
        foreach($ids as $id) {
            if(array_key_exists($id, $this->objects)) {
                $output[(int)$id] = $this->objects[$id];
            }
        }
        return $output;
    }
    
    protected function _saveMulti(array $vos = array()) {
        $this->objects = $vos + $this->objects;
        return true;
    }

    protected function _insert(Evaneos_Berthe_AbstractVO &$vo) {
        if ($vo->id) {
            $this->objects[(int)$vo->id] = $vo;
        }
        return true;
    }

    protected function _update(Evaneos_Berthe_AbstractVO &$vo) {
        if ($vo->id) {
            $this->objects[(int)$vo->id] = $vo;
        }
        return true;
    }

    protected function _delete(Evaneos_Berthe_AbstractVO &$vo) {
        if ($vo->id) {
            $id = (int) $vo->id;
            if (array_key_exists($id, $this->objects)) {
                unset($this->objects[$id]);
            }
        }
        return true;
    }
}