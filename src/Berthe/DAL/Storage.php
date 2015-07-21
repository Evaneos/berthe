<?php
namespace Berthe\DAL;

use Berthe\VO;
use Berthe\Fetcher;
use Berthe\Fetcher\Fetchable;

interface Storage extends Fetchable
{
    /**
     * @param Store   $store
     * @param boolean $isPrimary
     */
    public function addStore(Store $store, $isPrimary = true);

    /**
     * Set storage GUID
     *
     * @param string $guid
     * @return Storage
     */
    public function setStorageGUID($guid);

    /**
     * @param int $id
     * @return VO
     */
    public function getObjectFromPrimaryStore($id);

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
     * Saves a VO
     *
     * @param VO $vo
     * @return boolean
     */
    public function save(VO $vo);

    /**
     * Deletes a VO
     *
     * @param VO $vo
     */
    public function delete(VO $vo);

    /**
     * Deletes a VO by its id
     *
     * @param integer $id
     */
    public function deleteById($id);
}
