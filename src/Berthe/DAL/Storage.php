<?php
namespace Berthe\DAL;

use Berthe\VO;
use Berthe\Fetcher;

interface Storage
{
    /**
     * @param Store $store
     * @param boolean $isPrimary
     */
    function addStore(Store $store, $isPrimary = true);

    /**
     * Set storage GUID
     * @param string $guid
     * @return Storage
     */
    function setStorageGUID($guid);

    /**
     * @param int $id
     * @return VO
     */
    function getObjectFromPrimaryStore($id);

    /**
     * @param int $id
     * @return VO
     */
    function getById($id);

    /**
     * @param array $ids
     * @return VO[]
     */
    function getByIds(array $ids = array());

    /**
     * @param Fetcher $fetcher
     * @return Fetcher
     */
    function getByFetcher(Fetcher $fetcher);

    /**
     * Saves a VO
     * @param VO $vo
     * @return boolean
     */
    function save(VO $vo);

    /**
     * Deletes a VO
     * @param VO $vo
     */
    function delete(VO $vo);

    /**
     * Deletes a VO by its id
     * @param integer $vo
     */
    function deleteById($id);
}