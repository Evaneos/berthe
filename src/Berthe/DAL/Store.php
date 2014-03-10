<?php

namespace Berthe\DAL;

use Berthe\VO;
use Berthe\Fetcher;

interface Store {
    /**
     * Getter and setter for isEnabled toggle
     * @param mixed $bool bool|null
     * @return boolean
     */
    function isEnabled($bool = null);

    /**
     * @return boolean
     */
    function isPersistent();

    /**
     * @return boolean
     */
    function isTTLAble();

    /**
     * @param  Fetcher $fetcher
     * @return int
     */
    function getCountByFetcher(Fetcher $fetcher);

    /**
     * @param  Fetcher $fetcher
     * @return int
     */
    function getIdsByFetcher(Fetcher $fetcher);

    /**
     * @param array $ids
     * @return array id=>object
     */
    function load(array $ids = array());

    /**
     * @param VO $vo
     * @return boolean
     */
    function delete(VO $vo);

    /**
     * @param VO $vo
     * @return boolean success
     */
    function save(VO $vo);

    /**
     * Save multiple objects, returns true if all are saved, false if at least one failed
     * @param VO[] $vos
     * @return boolean false if at least one save has failed
     */
    function saveMulti(array $vos = array());
}