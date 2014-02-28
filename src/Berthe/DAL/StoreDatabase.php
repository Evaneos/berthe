<?php

namespace Berthe\DAL;

use Berthe\VO;
use Berthe\Fetcher;

class StoreDatabase extends AbstractStore {
    /**
     * @var Reader
     */
    protected $reader = null;
    /**
     * @var Writer
     */
    protected $writer = null;
    /**
     * @var boolean
     */
    protected $isPersistent = true;
    /**
     * @var boolean
     */
    protected $isTTLAble = false;

    public function getReader() {
        return $this->reader;
    }

    public function getWriter() {
        return $this->writer;
    }

    public function setReader(Reader $reader) {
        $this->reader = $reader;
        return $this;
    }

    public function setWriter(Writer $writer) {
        $this->writer = $writer;
        return $this;
    }

    public function getCountByFetcher (Fetcher $fetcher) {
        return $this->getReader()->selectCountByPaginator($fetcher);
    }

    public function getIdsByFetcher (Fetcher $fetcher) {
        return $this->getReader()->selectByPaginator($fetcher);
    }
    /**
     * @param array $ids
     * @return array id=>object
     */
    protected function _load(array $ids = array()) {
        return $this->getReader()->selectByIds($ids);
    }

    protected function _insert(VO $vo) {
        $ret = $this->getWriter()->insert($vo);
        if ($ret) {
            $results = $this->load(array($vo->getId()));
            if (count($results) === 1) {
                $vo = reset($results);
            }
        }
        return $ret;
    }

    protected function _update(VO $vo) {
        $ret = $this->getWriter()->update($vo);
        if ($ret) {
            $results = $this->load(array($vo->getId()));
            if (count($results) === 1) {
                $vo = reset($results);
            }
        }
        return $ret;
    }

    protected function _delete(VO $vo) {
        $ret = $this->getWriter()->delete($vo);
        return $ret;
    }
}