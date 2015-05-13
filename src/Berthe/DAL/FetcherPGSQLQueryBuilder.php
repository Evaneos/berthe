<?php

namespace Berthe\DAL;

use Berthe\Fetcher;
use Berthe\Fetcher\FetcherOperation;
use Berthe\Fetcher\Operation\ListOperation;
use Berthe\Fetcher\Operation\SimpleOperation;
use Berthe\Fetcher\Operation\OperationValue;

class FetcherPGSQLQueryBuilder implements FetcherQueryBuilder
{
    public function buildSort(Fetcher $fetcher)
    {
        $sorts = array();
        $_sorts = $fetcher->getSorts();

        foreach ($_sorts as $column => $sort) {
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
        } else {
            $offset = ($fetcher->getPage() - 1) * $fetcher->getNbByPage();
            $sql = sprintf(" LIMIT %s OFFSET %s", $fetcher->getNbByPage(), $offset);
            return $sql;
        }
    }

    public function buildFilters(Fetcher $fetcher)
    {
        list($filterInReq, $filterToParameter) = $this->buildOperation($fetcher);
        if ($fetcher->hasEmptyIN()) {
            $filterInReq = '1=2';
        } elseif ($filterInReq == '' || $filterInReq == '()') {
            $filterInReq = '1=1';
        }
        return array($filterInReq, $filterToParameter);
    }

    public function buildOperation(Fetcher $fetcher)
    {
        $rootOperation = $fetcher->getRootOperation();
        $operations = $rootOperation->getOperations();

        //We replace the operator by the groupName value from the fetcher
        foreach ($operations as $operation) {
            if ($operation instanceof ListOperation) {
                $realFilterOperator = $fetcher->getRealFilterOperator($operation->getGroupName());
                if ($realFilterOperator != null) {
                    $operation->setOperator($realFilterOperator);
                }
            }
        }

        //We replace the operator by the main operator value from the fetcher
        $rootOperation->setOperator($fetcher->getMainOperator());

        return $this->getOperationAsString($fetcher, $rootOperation);
    }

    /**
     * @param Fetcher $fetcher
     * @param FetcherOperation $operation
     * @return array query and params
     */
    protected function getOperationAsString(Fetcher $fetcher, FetcherOperation $operation)
    {
        $query = '';
        $params = array();

        if ($operation instanceof SimpleOperation) {
            $operator = $operation->getOperator();

            if ($operator === Fetcher::TYPE_IN) {
                $newOperation = new ListOperation(Fetcher::OPERATOR_OR);

                foreach ($operation->getValue() as $val) {
                    $newOperation->addOperation(
                        new SimpleOperation(Fetcher::TYPE_EQ, $operation->getColumnName(), $val)
                    );
                }

                list($query, $params) = $this->getOperationAsString($fetcher, $newOperation);
            } elseif ($operator === Fetcher::TYPE_NOT_IN) {
                $newOperation = new ListOperation(Fetcher::OPERATOR_AND);

                foreach ($operation->getValue() as $val) {
                    $newOperation->addOperation(
                        new SimpleOperation(Fetcher::TYPE_DIFF, $operation->getColumnName(), $val)
                    );
                }

                list($query, $params) = $this->getOperationAsString($fetcher, $newOperation);
            } else {
                if ($operator === Fetcher::TYPE_CUSTOM) {
                    $value = $operation->getValue();
                    if (!($value instanceof OperationValue)) {
                        throw new \Exception('OperationValue expected');
                    }

                    list($operationValueQuery, $operationValueParams) = $value->getOperationValue($operation->getColumnName());
                    if (empty($operationValueParams)) {
                        $query .= $operationValueQuery;
                    } else {
                        $query .= vsprintf($operationValueQuery, array_fill(0, count($operationValueParams), '?'));
                        foreach ($operationValueParams as $valueParam) {
                            $params[] = $valueParam;
                        }
                    }
                } else {
                    $query  = $fetcher->strColumnFilterToDbNotation($operation->getColumnName(), $operator);
                    $query .= ' ' ;
                    $query .= $fetcher->strFilterToDbNotation($operator);

                    if ($operator !== Fetcher::TYPE_IS_NOT_NULL && $operator !== Fetcher::TYPE_IS_NULL) {
                        // If the operation has 2 members
                        $params[] = $this->getOperationValue($operation);
                    }
                }
            }
        } elseif ($operation instanceof ListOperation) {
            $operations = $operation->getOperations();
            $strings = array();

            foreach ($operations as $currentOperation) {
                list($returnQuery, $returnParams) = $this->getOperationAsString($fetcher, $currentOperation);

                $strings[] = $returnQuery;
                $params = array_merge($params, $returnParams);
            }

            $query = implode($operation->getOperator(), $strings);
        } else {
            throw new \InvalidArgumentException('Givent operation is not supported');
        }

        return array('('.$query.')', $params);
    }

    protected function getOperationValue(SimpleOperation $operation)
    {
        switch ($operation->getOperator()) {
            case Fetcher::TYPE_LIKE :
            case Fetcher::TYPE_ILIKE :
                return '%' . $operation->getValue() . '%';
            case Fetcher::TYPE_LOWERED_EQ :
                return strtolower($operation->getValue());
            default :
                return $operation->getValue();
        }
    }
}
