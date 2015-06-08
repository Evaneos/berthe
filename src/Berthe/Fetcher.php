<?php

namespace Berthe;

use Berthe\Fetcher\Operation\ListOperation;
use Berthe\Fetcher\FetcherOperation;
use Berthe\Fetcher\Operation\SimpleOperation;

class Fetcher extends Paginator implements \Serializable
{
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
    const TYPE_NOT_IN = 14;

    const TYPE_ARRAY_CONTAINS = 15;
    const TYPE_ARRAY_LENGTH = 16;

    const OPERATOR_AND = ' AND ';
    const OPERATOR_OR  = ' OR ';

    const SORT_ASC = 'ASC';
    const SORT_DESC = 'DESC';

    protected $filters = array();
    protected $filtersOperator = array();

    /**
     *
     * @var ListOperation
     */
    protected $rootOperation = null;

    protected $sorts = array();
    protected $isRandomSort = false;
    protected $hasEmptyIN = false;
    protected $mainOperator = self::OPERATOR_AND;

    public function __construct($page = 1, $nbByPage = 25, array $elements = array())
    {
        parent::__construct($page, $nbByPage, $elements);
        $this->rootOperation = new ListOperation($this->mainOperator);
    }

    public function sortById($direction)
    {
        $this->addSort('id', $direction);
    }

    public function hasEmptyIN()
    {
        return $this->hasEmptyIN;
    }

    /**
     *
     * @deprecated
     *
     * @param string $columnName
     * @param int $typeFilter
     * @param string $value
     * @param mixed $groupName false if not in group, string if in group
     *
     * @return Fetcher
     */

    protected function addFilter($columnName, $typeFilter, $value, $groupName = false) {
        if (is_array($value) &&
            $typeFilter != self::TYPE_ARRAY_CONTAINS &&
            $typeFilter != self::TYPE_ARRAY_LENGTH) {
            $this->addFilters($columnName, $typeFilter, $value, $groupName);
            return $this;
        }

        $this->filters[] = array(
            self::FILTER_TYPE => $typeFilter,
            self::FILTER_VALUE => $value,
            self::FILTER_COLUMN => $columnName,
            self::FILTER_GROUP_NAME => $groupName ? $groupName : count($this->filters)
        );

        $this->addFilterOperation(new SimpleOperation($typeFilter, $columnName, $value, $groupName?$groupName:$columnName));

        return $this;
    }

    /**
     *
     * @param FetcherOperation $operation
     * @throws \InvalidArgumentException
     *
     * @return \Berthe\Fetcher
     */
    protected function addFilterOperation(FetcherOperation $operation)
    {
        $newOperation = $operation;
        $groupName = $operation->getGroupName();
        $previousOperation = $this->rootOperation->getOperation($groupName);

        //If there's already an operation for that group
        if ($previousOperation != null) {
            $groupFilterOperator = $this->getFilterOperator($groupName);

            $createNewList = true;
            if ($previousOperation instanceof ListOperation) {
                //If the previous operation is a list containing the groupName, we don't create a new list
                $subOperations = $previousOperation->getOperations();
                foreach ($subOperations as $subOperation) {
                    if ($subOperation->getGroupName() == $groupName) {
                        $createNewList = false;
                        break;
                    }
                }
            }

            if ($createNewList) {
                //We crate a list operation with the name of the group
                $newOperation = new ListOperation($groupFilterOperator, $groupName);
                //We add the retrieved operation to the new operation created
                $newOperation->addOperation($previousOperation);
            } else {
                $newOperation = $previousOperation;
            }

            //We add the new operation to the list operation retrieved/created
            $newOperation->addOperation($operation);
        }

        //If it's the first for its group, we keep it unchanged
        $this->rootOperation->addOperation($newOperation);

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
    protected function addFilters($columnName, $typeFilter, array $values, $groupName = false)
    {
        if ($typeFilter === self::TYPE_IN && count($values) === 0) {
            $this->hasEmptyIN = true;
        } else {
            if ($typeFilter === self::TYPE_IN) {
                $typeFilter = self::TYPE_EQ;
            }
            if ($typeFilter === self::TYPE_NOT_IN) {
                $newOperation = new ListOperation(Fetcher::OPERATOR_AND);
                $this->addFilterOperation($newOperation);
                foreach ($values as $val) {
                    $newOperation->addOperation(
                        new SimpleOperation(Fetcher::TYPE_DIFF, $columnName, $val)
                    );
                }
                return $this;
            }
            foreach ($values as $value) {
                $this->addFilter($columnName, $typeFilter, $value, $groupName);
            }
        }

        return $this;
    }

    /**
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @return ListOperation
     */
    public function getRootOperation()
    {
        return $this->rootOperation;
    }

    public function getFilterColumns()
    {
        return array_unique($this->getColumnsFromOperation($this->rootOperation));
    }

    private function getColumnsFromOperation(FetcherOperation $operation)
    {
        $columns = array();

        if ($operation instanceof SimpleOperation) {
            $columns[] = $operation->getColumnName();
        } elseif ($operation instanceof ListOperation) {
            $operations = $operation->getOperations();
            foreach ($operations as $curOperation) {
                $columns = array_merge($columns, $this->getColumnsFromOperation($curOperation));
            }
        } else {
            throw new \InvalidArgumentException('Invalid operation!');
        }

        return $columns;
    }

    /**
     * @param array $filters
     * @throws Exception
     */
    protected function setFilters(array $filters = array())
    {
        foreach ($filters as $filter) {
            $bCol = array_key_exists(self::FILTER_COLUMN, $filter);
            $bType = array_key_exists(self::FILTER_TYPE, $filter);
            $bVal = array_key_exists(self::FILTER_VALUE, $filter);

            if ($bCol && $bTYpe && $bVal) {
                $this->addFilter($filter[self::FILTER_COLUMN], $filter[self::FILTER_TYPE], $filter[self::FILTER_VALUE]);
            } else {
                throw new \RuntimeException('Invalid filter');
            }
        }
    }

    /**
     * @param string $columnName
     * @param string $operator
     */
    public function setFilterOperator($columnName, $operator)
    {
        $this->filtersOperator[$columnName] = $operator;
    }

    /**
     * @param string $columnName
     * @return string
     */
    public function getFilterOperator($columnName)
    {
        $realFilterOperator = $this->getRealFilterOperator($columnName);
        if ($realFilterOperator == null) {
            return self::OPERATOR_OR;
        }
        return $realFilterOperator;
    }

    public function getRealFilterOperator($columnName)
    {
        if (array_key_exists($columnName, $this->filtersOperator)) {
            return $this->filtersOperator[$columnName];
        }
        return null;
    }

    /**
     *
     * @param int $type
     * @return array
     */
    public function getFiltersByType($type)
    {
        //TODO get with the new operations
        $output = array();
        foreach ($this->filters as $filter) {
            if ($filter[self::FILTER_TYPE] === $type) {
                $output[] = $filter;
            }
        }
        return $output;
    }

    /**
     *
     * @param string $columnName
     * @param mixed $sortType
     * @return Fetcher
     */
    protected function addSort($columnName, $sortType)
    {
        if (is_bool($sortType)) {
            $sortType = $sortType ? self::SORT_DESC : self::SORT_ASC;
        }

        if ($sortType != self::SORT_ASC && $sortType != self::SORT_DESC) {
            throw new \InvalidArgumentException(sprintf("Trying to add a sort in unknown direction, requested '%s'", $direction), 500);
        }

        $this->sorts[$columnName] = $sortType;

        return $this;
    }

    /**
     * @param boolean $boolean
     * @return Fetcher
     */
    public function setRandomSort($boolean)
    {
        $this->isRandomSort = $boolean;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isRandomSort()
    {
        return $this->isRandomSort;
    }

    /**
     *
     * @return array
     */
    public function getSorts()
    {
        return $this->sorts;
    }

    /**
     * @param array $sorts
     * @throws Exception
     */
    protected function setSorts(array $sorts = array())
    {
        foreach ($sorts as $sort) {
            if (count($sort) != 2) {
                throw new \Exception("Invalid sort");
            }
            $this->addSort($sort[0], $sort[1]);
        }
    }

    public function strFilterToDbNotation($type)
    {
        switch ($type) {
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

    public function strColumnFilterToDbNotation($colName, $type)
    {
        switch ($type) {
            case self::TYPE_LOWERED_EQ : return 'LOWER(' . $colName . ')';
        }

        return $colName;
    }

    /**
     * Sets the main operator (AND or OR)
     * @param string $operator self::OPERATOR_AND or self::OPERATOR_OR
     * @throws InvalidArgumentException
     */
    public function setMainOperator($operator)
    {
        switch ($operator) {
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
        $this->rootOperation->setOperator($this->mainOperator);
    }

    public function getMainOperator()
    {
        return $this->mainOperator;
    }

    /**
     * @param int $page
     * @param int $nbByPage
     * @return Fetcher
     */
    public function copy($page = null, $nbByPage = null)
    {
        if (!$page) {
            $page       = $this->getPage();
        }
        if (!$nbByPage) {
            $nbByPage   = $this->getNbByPage();
        }

        $copy = new static($page, $nbByPage);
        $copy->filters = $this->filters;
        $copy->rootOperation = $this->rootOperation;
        $copy->sorts = $this->sorts;
        $copy->filtersOperator = $this->filtersOperator;

        return $copy;
    }

    public function __toArray()
    {
        return get_object_vars($this);
    }

    public function serialize()
    {
        return serialize($this->__toArray());
    }

    public function unserialize($serializedData)
    {
        $datas = unserialize($serializedData);
        foreach ($datas as $key => $value) {
            $this->$key = $value;
        }
    }
}
