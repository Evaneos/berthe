<?php
class Evaneos_Berthe_StoreDatabase extends Evaneos_Berthe_AbstractStore {
    /**
     * @var Evaneos_Berthe_AbstractReader
     */
    protected $_reader = null;
    /**
     * @var Evaneos_Berthe_AbstractWriter
     */
    protected $_writer = null;
    /**
     * @var boolean
     */
    protected $isPersistent = true;
    /**
     * @var boolean
     */
    protected $isTTLAble = false;
    
    public function __construct(Evaneos_Berthe_AbstractReader $reader, Evaneos_Berthe_AbstractWriter $writer) {
        $this->_reader = $reader;
        $this->_writer = $writer;
    }
    
    /**
     * @param array $ids
     * @return array id=>object
     */
    protected function _load(array $ids = array()) {
        return $this->_reader->selectByIds($ids);
    }

    protected function _insert(Evaneos_Berthe_AbstractVO &$vo) {
        $ret = $this->_writer->insert($vo);
        if ($ret) {
            $results = $this->load(array($vo->id));
            if (count($results) === 1) {
                $vo = reset($results);
            }
        }
        return $ret;
    }

    protected function _update(Evaneos_Berthe_AbstractVO &$vo) {
        $ret = $this->_writer->update($vo);
        if ($ret) {
            $results = $this->load(array($vo->id));
            if (count($results) === 1) {
                $vo = reset($results);
            }
        }
        return $ret;
    }

    protected function _delete(Evaneos_Berthe_AbstractVO &$vo) {
        $ret = $this->_writer->delete($vo);
        return $ret;
    }
}