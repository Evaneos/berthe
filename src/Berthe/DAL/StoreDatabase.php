<?php

namespace Evaneos\Berthe\DAL;

class Berthe_StoreDatabase extends Berthe_AbstractStore {
    /**
     * @var Berthe_AbstractReader
     */
    protected $_reader = null;
    /**
     * @var Berthe_AbstractWriter
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

    public function __construct(Berthe_AbstractReader $reader, Berthe_AbstractWriter $writer) {
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

    protected function _insert(Berthe_AbstractVO &$vo) {
        $ret = $this->_writer->insert($vo);
        if ($ret) {
            $results = $this->load(array($vo->id));
            if (count($results) === 1) {
                $vo = reset($results);
            }
        }
        return $ret;
    }

    protected function _update(Berthe_AbstractVO &$vo) {
        $ret = $this->_writer->update($vo);
        if ($ret) {
            $results = $this->load(array($vo->id));
            if (count($results) === 1) {
                $vo = reset($results);
            }
        }
        return $ret;
    }

    protected function _delete(Berthe_AbstractVO &$vo) {
        $ret = $this->_writer->delete($vo);
        return $ret;
    }
}