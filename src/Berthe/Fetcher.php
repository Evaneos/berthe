<?php

namespace Berthe;

class Fetcher extends Paginator {
    protected $filters = array();
    protected $filtersOperator = array();
    protected $sorts = array();
    protected $isRandomSort = false;
    protected $hasEmptyIN = false;
    protected $mainOperator = self::OPERATOR_AND;

    const FILTER_TYPE = 1;
    const FILTER_VALUE = 2;
    const FILTER_COLUMN = 3;
    const FILTER_GROUP_NAME = 4;

    const TYPE_EQ = 1;
    const TYPE_LIKE = 2;
    const TYPE_ILIKE = 3;
    const TYPE_DIFF = 4;
    const TYPE_SUP = 5;
    const TYPE_SUP_STRICT = 6;
    const TYPE_INF = 7;
    const TYPE_INF_STRICT = 8;
    const TYPE_CUSTOM = 9;
    const TYPE_IS_NULL = 10;
    const TYPE_IS_NOT_NULL = 11;
    const TYPE_LOWERED_EQ = 12;
    const TYPE_IN = 13;

    const OPERATOR_AND = ' AND ';
    const OPERATOR_OR  = ' OR ';

    const SORT_ASC = 'ASC';
    const SORT_DESC = 'DESC';

    public function sortById($direction) {
        if (in_array(array(self::SORT_ASC, self::SORT_DESC), $direction)) {
            $this->addSort('id', $direction);
        }
        else {
            throw new \InvalidArgumentException(sprintf("Trying to add a sort in unknown direction, requested '%s'", $direction), 500);
        }
    }

    public function hasEmptyIN() {
        return $this->hasEmptyIN;
    }

    /**
     *
     * @param string $columnName
     * @param int $typeFilter
     * @param string $value
     * @param mixed $groupName false if not in group, string if in group
     * @return Fetcher
     */
    protected function addFilter($columnName, $typeFilter, $value, $groupName = false) {
        if ($typeFilter === self::TYPE_IN) {
            $typeFilter = self::TYPE_EQ;
        }

        $this->filters[] = array(
            self::FILTER_TYPE => $typeFilter,
            self::FILTER_VALUE => $value,
            self::FILTER_COLUMN => $columnName,
            self::FILTER_GROUP_NAME => $groupName ? $groupName : count($this->filters)
        );

        return $this;
    }

    /**
     *
     * @param string $columnName
     * @param int $typeFilter
     * @param string $values
     * @param mixed $groupName false if not in group, string if in group
     * @return Fetcher
     */
    protected function addFilters($columnName, $typeFilter, array $values, $groupName = false) {
        if ($typeFilter === self::TYPE_IN && count($values) === 0) {
            $this->hasEmptyIN = true;
        }
        else {
            foreach($values as $value) {
                $this->addFilter($columnName, $typeFilter, $value, $groupName);
            }
        }

        return $this;
    }

    /**
     *
     * @return array
     */
    public function getFilters() {
        return $this->filters;
    }

    /**
     * @param array $filters
     * @throws Exception
     */
    protected function setFilters(array $filters = array()) {
        foreach($filters as $filter) {
            $bCol = array_key_exists(self::FILTER_COLUMN, $filter);
            $bType = array_key_exists(self::FILTER_TYPE, $filter);
            $bVal = array_key_exists(self::FILTER_VALUE, $filter);

            if ($bCol && $bTYpe && $bVal) {
                $this->addFilter($filter[self::FILTER_COLUMN], $filter[self::FILTER_TYPE], $filter[self::FILTER_VALUE]);
            }
            else {
                throw new \RuntimeException('Invalid filter');
            }
        }
    }

    /**
     * @param string $columnName
     * @param string $operator
     */
    public function setFilterOperator($columnName, $operator) {
        $this->filtersOperator[$columnName] = $operator;
    }

    /**
     * @param string $columnName
     * @return string
     */
    public function getFilterOperator($columnName) {
        if (array_key_exists($columnName, $this->filtersOperator)) {
            return $this->filtersOperator[$columnName];
        }
        else {
            return self::OPERATOR_OR;
        }
    }

    /**
     *
     * @param int $type
     * @return array
     */
    public function getFiltersByType($type) {
        $output = array();
        foreach($this->filters as $filter) {
            if ($filter[self::FILTER_TYPE] === $type) {
                $output[] = $filter;
            }
        }
        return $output;
    }

    /**
     *
     * @param string $columnName
     * @param string $sortType
     * @return Fetcher
     */
    protected function addSort($columnName, $sortType) {
        if ($sortType != self::SORT_ASC && $sortType != self::SORT_DESC) {
            return;
        }

        $this->sorts[$columnName] = $sortType;

        return $this;
    }

    /**
     * @param boolean $boolean
     * @return Fetcher
     */
    public function setRandomSort($boolean) {
        $this->isRandomSort = $boolean;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isRandomSort() {
        return $this->isRandomSort;
    }

    /**
     *
     * @return array
     */
    public function getSorts() {
        return $this->sorts;
    }

    /**
     * @param array $sorts
     * @throws Exception
     */
    protected function setSorts(array $sorts = array()) {
        foreach($sorts as $sort) {
            if (count($sort) != 2) {
                throw new \Exception("Invalid sort");
            }
            $this->addSort($sort[0], $sort[1]);
        }
    }

    public function strFilterToDbNotation($type) {
        switch($type) {
            case self::TYPE_DIFF : return ' != ? ';
            case self::TYPE_EQ : return ' = ? ';
            case self::TYPE_LOWERED_EQ : return ' = ? ';
            case self::TYPE_ILIKE : return ' ILIKE ? ';
            case self::TYPE_INF : return ' <= ? ';
            case self::TYPE_INF_STRICT : return ' < ? ';
            case self::TYPE_LIKE : return ' LIKE ? ';
            case self::TYPE_SUP : return ' >= ? ';
            case self::TYPE_SUP_STRICT : return ' > ? ';
            case self::TYPE_IS_NULL : return ' IS NULL ';
            case self::TYPE_IS_NOT_NULL : return ' IS NOT NULL ';
            case self::TYPE_CUSTOM : return ' ';
        }
        return null;
    }

    public function strColumnFilterToDbNotation($colName, $type) {
        switch($type) {
            case self::TYPE_LOWERED_EQ : return 'LOWER(' . $colName . ')';
        }

        return $colName;
    }

    /**
     * Sets the main operator (AND or OR)
     * @param string $operator self::OPERATOR_AND or self::OPERATOR_OR
     * @throws InvalidArgumentException
     */
    public function setMainOperator($operator) {
        switch($operator) {
            case self::OPERATOR_AND :
                $this->mainOperator = self::OPERATOR_AND;
                break;
            case self::OPERATOR_OR :
                $this->mainOperator = self::OPERATOR_OR;
                break;
            default :
                throw new \InvalidArgumentException(__CLASS__ . '::' . __FUNCTION__ . '() First argument should be ' . __CLASS__ . '::OPERATOR_AND or ' . __CLASS__ . '::OPERATOR_OR constant');
                break;
        }
    }

    public function getMainOperator() {
        return $this->mainOperator;
    }

    /**
     * @param int $page
     * @param int $nbByPage
     * @return Fetcher
     */
    public function copy($page = null, $nbByPage = null) {
        if (!$page)     {       $page       = $this->getPage();         }
        if (!$nbByPage) {       $nbByPage   = $this->getNbByPage();     }

        $copy = new static($page, $nbByPage);
        $copy->filters = $this->filters;
        $copy->sorts = $this->sorts;
        $copy->filtersOperator = $this->filtersOperator;

        return $copy;
    }
}