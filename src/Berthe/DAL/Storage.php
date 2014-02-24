<?php
namespace Berthe\DAL;

use Berthe\VO;
use Berthe\Fetcher;

interface Storage
{
    const STORE_LEVEL_1 = 'array_php';
    const STORE_VOLATILE_WITH_TTL = 'ramcache';
    const STORE_PERSISTENT = 'database';

    /**
     * Set storage GUID
     * @param string $guid
     * @return Storage
     */
    public function setStorageGUID($guid);

    /**
     * @return StoreDatabase
     */
    public function getStorePersistent();

    public function setStorePersistent(StoreDatabase $store);

    /**
     * @return Store
     */
    public function getStoreVolatile();

    public function setStoreVolatile(Store $store);

    /**
     * @return Store
     */
    public function getStoreLevel1();

    public function setStoreLevel1(Store $store);

    /**
     * @param int $id
     * @return VO
     */
    public function getOriginalObject($id);

    /**
     * @param int $id
     * @return VO
     */
    public function getById($id);

    /**
     * @param array $ids
     * @return VO[]
     */
    public function getByIds(array $ids = array());

    /**
     * @param array $ids
     * @return VO[]
     */
    public function getColumnByIds(array $ids = array(), $columnName = 'id');

    /**
     * @param array $ids
     * @return VO[]
     */
    public function getColumnByIdsPreserveIds(array $ids = array(), $columnName = 'id');

    /**
     * @param Fetcher $paginator
     * @return Fetcher
     */
    public function getByPaginator(Fetcher $paginator);

    /**
     * @param Fetcher $paginator
     * @param string $columnName
     * @return Fetcher
     */
    public function getColumnByPaginator(Fetcher $paginator, $columnName = 'id');

    /**
     * @param Fetcher $paginator
     * @param string $columnName
     * @return Fetcher
     */
    public function getColumnByPaginatorPreserveIds(Fetcher $paginator, $columnName = 'id');

    /**
     * @param Fetcher $paginator
     * @return string sql
     */
    public function getSqlByPaginator(Fetcher $paginator);

    /**
     * TODO optimize that one !
     * @param VO $vo
     * @param Fetcher $paginator
     * @param int $nbBefore
     * @param int $nbAfter
     * @param bool $loop
     * @return array [VO[], VO[]]  BEFORE / AFTER
     */
    public function getNextAndPreviousByPaginator(VO $vo, Fetcher $paginator, $nbBefore = 1, $nbAfter = 1, $loop = false);

    /**
     * Determines if cache is to be forced
     * @return boolean
     */
    public function ignoreCache($shallIgnore = null);

    /**
     * Set to true to ignore object existence in $_objects when fetching
     * @param boolean $shallIgnore
     */
    public function ignoreCacheLevel1($shallIgnore = null);

    /**
     * @param mixed $shallIgnore
     * @return type
     */
    public function ignoreAllCache($shallIgnore = null);

    /**
     * Saves a VO
     * @param VO $vo
     * @return boolean
     */
    public function save(VO $vo);

    /**
     * Deletes a VO
     * @param VO $vo
     */
    public function delete(VO $vo);

    /**
     * Deletes a VO by its id
     * @param integer $vo
     */
    public function deleteById($id);
}