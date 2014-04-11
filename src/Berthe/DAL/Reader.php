<?php

namespace Berthe\DAL;

interface Reader {
    /**
     * @param Translator $translator
     */
    function setTranslator(Translator $translator);
    /**
     * Returns the Class name of the VO for current package
     * @return string
     */
    function getVOFQCN();

    /**
     * Returns the name of the primary key column.
     * @return string
     */
    function getIdentityColumn();

    /**
     * Gets a bunch of \Berthe\AbstractVO from database from their ids
     * @param array $ids
     * @return \Berthe\VO
     */
    function selectByIds(array $ids = array ());

    /**
     * Returns the values for $col column in the select query for $ids ids
     * @param array $ids
     * @param string $columnName
     * @return mixed[]
     * @throws InvalidArgumentException
     */
    function selectColByIdsPreserveIds(array $ids = array(), $columnName = 'id');

    /**
     * Returns the values for $col column in the select query for $ids ids
     * @param array $ids
     * @param string $columnName
     * @return mixed[]
     * @throws InvalidArgumentException
     */
    function selectColByIds(array $ids = array(), $columnName = 'id');

    /**
     * @param \Berthe\Fetcher $fetcher
     * @return \Berthe\Fetcher
     */
    function selectCountByFetcher(\Berthe\Fetcher $fetcher);

    /**
     * @param \Berthe\Fetcher $fetcher
     * @return \Berthe\Fetcher
     */
    function selectByFetcher(\Berthe\Fetcher $fetcher);

    /**
     * @param \Berthe\Fetcher $fetcher
     * @return array(string, array) the sql and the array of the parameters
     */
    function getSqlByFetcher(\Berthe\Fetcher $fetcher);

}
