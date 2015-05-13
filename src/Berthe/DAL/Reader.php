<?php

namespace Berthe\DAL;

interface Reader
{
    /**
     * @param Translator $translator
     */
    public function setTranslator(Translator $translator);
    /**
     * Returns the Class name of the VO for current package
     * @return string
     */
    public function getVOFQCN();

    /**
     * Returns the name of the primary key column.
     * @return string
     */
    public function getIdentityColumn();

    /**
     * Gets a bunch of \Berthe\AbstractVO from database from their ids
     * @param array $ids
     * @return \Berthe\VO
     */
    public function selectByIds(array $ids = array());

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
     * @param \Berthe\Fetcher $fetcher
     * @return \Berthe\Fetcher
     */
    public function selectCountByFetcher(\Berthe\Fetcher $fetcher);

    /**
     * @param \Berthe\Fetcher $fetcher
     * @return \Berthe\Fetcher
     */
    public function selectByFetcher(\Berthe\Fetcher $fetcher);

    /**
     * @param \Berthe\Fetcher $fetcher
     * @return array(string, array) the sql and the array of the parameters
     */
    public function getSqlByFetcher(\Berthe\Fetcher $fetcher);
}
