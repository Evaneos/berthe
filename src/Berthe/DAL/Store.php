<?php

namespace Berthe\DAL;

use Berthe\VO;
use Berthe\Fetcher;

interface Store
{
    /**
     * Getter and setter for isEnabled toggle
     * @param mixed $bool bool|null
     * @return boolean
     */
    public function isEnabled($bool = null);

    /**
     * @return boolean
     */
    public function isPersistent();

    /**
     * @return boolean
     */
    public function isTTLAble();

    /**
     * @param  Fetcher $fetcher
     * @return int
     */
    public function getCountByFetcher(Fetcher $fetcher);

    /**
     * @param  Fetcher $fetcher
     * @return int
     */
    public function getIdsByFetcher(Fetcher $fetcher);

    /**
     * @param array $ids
     * @return array id=>object
     */
    public function load(array $ids = array());

    /**
     * @param VO $vo
     * @return boolean
     */
    public function delete(VO $vo);

    /**
     * @param VO $vo
     * @return boolean success
     */
    public function save(VO $vo);

    /**
     * Save multiple objects, returns true if all are saved, false if at least one failed
     * @param VO[] $vos
     * @return boolean false if at least one save has failed
     */
    public function saveMulti(array $vos = array());
}
