<?php

namespace Berthe\DAL;
use Berthe\Fetcher;

class FetcherPGSQLQueryBuilder implements FetcherQueryBuilder
{
    public function buildSort(Fetcher $fetcher)
    {
        $sorts = array();
        $_sorts = $fetcher->getSorts();

        foreach($_sorts as $column => $sort) {
            $sorts[] = $column . ' ' . $sort;
        }

        if (count($sorts) < 1) {
            $sorts[] = "id ASC";
        }

        return implode(", ", $sorts);
    }

    public function buildLimit(Fetcher $fetcher)
    {
        if (!$fetcher->hasLimit()) {
            return '';
        }
        else {
            $offset = ($fetcher->getPage() - 1) * $fetcher->getNbByPage();
            $sql = sprintf(" LIMIT %s OFFSET %s", $fetcher->getNbByPage(), $offset);
            return $sql;
        }
    }

    public function buildFilters(Fetcher $fetcher)
    {
        $_filters = $fetcher->getFilters();

        $_filterByCol = array();

        foreach($_filters as $filter) {
            if (!array_key_exists($filter[Fetcher::FILTER_COLUMN], $_filterByCol)) {
                $_filterByCol[$filter[Fetcher::FILTER_COLUMN]] = array();
            }

            if (!array_key_exists($filter[Fetcher::FILTER_GROUP_NAME], $_filterByCol[$filter[Fetcher::FILTER_COLUMN]])) {
                $_filterByCol[$filter[Fetcher::FILTER_COLUMN]][$filter[Fetcher::FILTER_GROUP_NAME]] = array();
            }

            $_filterByCol[$filter[Fetcher::FILTER_COLUMN]][$filter[Fetcher::FILTER_GROUP_NAME]][] = $filter;
        }

        $aToParameter = array();
        if($fetcher->getMainOperator() != Fetcher::OPERATOR_OR) {
            $aImplodedColumns = array("1=1");
            if ($fetcher->hasEmptyIN()) {
                $aImplodedColumns[] = "1=2";
            }
        } else {
            $aImplodedColumns = array();
        }

        foreach($_filterByCol as $columnName => $groups) {
            $aToColumn = array();
            foreach($groups as $filters) {
                $aToGroup = array();
                foreach($filters as $filter) {
                    if ($filter[Fetcher::FILTER_TYPE] == Fetcher::TYPE_IS_NULL) {
                        $aToGroup[] = $fetcher->strColumnFilterToDbNotation($columnName, $filter[Fetcher::FILTER_TYPE]) . ' ' . $fetcher->strFilterToDbNotation($filter[Fetcher::FILTER_TYPE]);
                    }
                    else if ($filter[Fetcher::FILTER_TYPE] == Fetcher::TYPE_IS_NOT_NULL) {
                        $aToGroup[] = $fetcher->strColumnFilterToDbNotation($columnName, $filter[Fetcher::FILTER_TYPE]) . ' ' . $fetcher->strFilterToDbNotation($filter[Fetcher::FILTER_TYPE]);
                    }
                    else {
                        $aToGroup[] = $fetcher->strColumnFilterToDbNotation($columnName, $filter[Fetcher::FILTER_TYPE]) . ' ' . $fetcher->strFilterToDbNotation($filter[Fetcher::FILTER_TYPE]);
                        switch($filter[Fetcher::FILTER_TYPE]) {
                            case Fetcher::TYPE_LIKE :
                            case Fetcher::TYPE_ILIKE :
                                $aToParameter[] = '%' . $filter[Fetcher::FILTER_VALUE] . '%';
                                break;
                            case Fetcher::TYPE_LOWERED_EQ :
                                $aToParameter[] = strtolower($filter[Fetcher::FILTER_VALUE]);
                                break;
                            default :
                                $aToParameter[] = $filter[Fetcher::FILTER_VALUE];
                                break;
                        }
                    }
                }
                $aToColumn[] = "(" . implode(Fetcher::OPERATOR_AND, $aToGroup) . ")";
            }

            $implodeColumn = "(" . implode($fetcher->getFilterOperator($columnName), $aToColumn) . ")";
            $aImplodedColumns[] = $implodeColumn;
        }

        $strQuery = implode($fetcher->getMainOperator(), $aImplodedColumns);

        return array($strQuery, $aToParameter);
    }
}
