<?php

namespace Berthe\DAL;

interface Reader {
   
    /**
     * Returns the Class name of the VO for current package
     * @return string
     */
    public function getVOClass();
    
    /**
     * Returns the name of the primary key column.
     * @return string
     */
    public function getIdentityColumn() {
        return 'id';
    }

    /**
     * Gets a bunch of \Berthe\AbstractVOVO from database from their ids
     * @param array $ids
     * @return \Berthe\VO
     */
    public function selectByIds(array $ids = array ());

    /**
     * Returns the values for $col column in the select query for $ids ids
     * @param array $ids
     * @param string $columnName
     * @return mixed[]
     * @throws InvalidArgumentException
     */
    public function selectColByIdsPreserveIds(array $ids = array(), $columnName = 'id');

    /**
     * Returns the values for $col column in the select query for $ids ids
     * @param array $ids
     * @param string $columnName
     * @return mixed[]
     * @throws InvalidArgumentException
     */
    public function selectColByIds(array $ids = array(), $columnName = 'id');
  
     /**
     * @param Fetcher $paginator
     * @return Fetcher
     */
    public function selectCountByPaginator(\Berthe\Fetcher $paginator);

    /**
     * @param Fetcher $paginator
     * @return Fetcher
     */
    public function selectByPaginator(\Berthe\Fetcher $paginator);

    /**
     * @param Fetcher $paginator
     * @return array(string, array) the sql and the array of the parameters
     */
    public function getSqlByPaginator(\Berthe\Fetcher $paginator);

}
