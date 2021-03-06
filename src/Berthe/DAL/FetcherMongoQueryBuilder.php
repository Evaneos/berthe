<?php
namespace Berthe\DAL;

use Berthe\Fetcher;
use Berthe\Fetcher\FetcherOperation;
use Berthe\Fetcher\Operation\ListOperation;
use Berthe\Fetcher\Operation\SimpleOperation;
use Berthe\DAL\FetcherQueryBuilder;

class FetcherMongoQueryBuilder implements FetcherQueryBuilder
{
    public function buildSort(Fetcher $fetcher)
    {
        $sorts = array();
        $_sorts = $fetcher->getSorts();

        foreach ($_sorts as $column => $sort) {
            $sorts[$column] = ($sort === Fetcher::SORT_ASC) ? 1 : -1;
        }

        if (count($sorts) < 1) {
            $sorts['_id'] = 1;
        }

        return $sorts;
    }

    public function buildLimit(Fetcher $fetcher)
    {
        $offset = null;
        $limit = null;

        if ($fetcher->hasLimit()) {
            $offset = ($fetcher->getPage() - 1) * $fetcher->getNbByPage();
            $limit = $fetcher->getNbByPage();
        }
        return array('offset'=> (int)$offset, 'limit' => (int)$limit);
    }

    public function buildFilters(Fetcher $fetcher)
    {
        $filters = $this->buildOperation($fetcher);

        if ($fetcher->hasEmptyIN()) {
            $filters = array('1'=>'2');
        }

        return $filters;
    }

    protected function buildOperation(Fetcher $fetcher)
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


        return $this->getOperationAsArray($rootOperation);

    }

    /**
     * @param FetcherOperation $operation
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function getOperationAsArray(FetcherOperation $operation)
    {
        $filters = array();

        if ($operation instanceof SimpleOperation) {
            $filters = $this->addSimpleOperation($operation);
        } else if ($operation instanceof ListOperation) {
            $operations = $operation->getOperations();

            if ($operation->getOperator() === Fetcher::OPERATOR_OR) {
                $filters['$or'] = array_map(
                    function(FetcherOperation $operation) {
                        return $this->getOperationAsArray($operation);
                    },
                    $operations
                );
            } else {
                foreach ($operations as $currentOperation) {
                    $currentFilters = $this->getOperationAsArray($currentOperation);
                    $filters = array_merge_recursive($filters, $currentFilters);
                }
            }
        } else {
            throw new \InvalidArgumentException('Given operation is not supported');
        }

        return $filters;
    }

    protected function addSimpleOperation(SimpleOperation $operation)
    {
        switch ($operation->getOperator()) {
            case Fetcher::TYPE_EQ:
                return array($operation->getColumnName()=>array('$eq'=>$operation->getValue()));
            case Fetcher::TYPE_IN:
                return array($operation->getColumnName()=>array('$in'=>$operation->getValue()));
            case Fetcher::TYPE_ARRAY_CONTAINS:
                return array($operation->getColumnName()=>array('$all'=>$operation->getValue()));
            case Fetcher::TYPE_ARRAY_LENGTH:
                return array($operation->getColumnName()=>array('$size'=>$operation->getValue()));
            case Fetcher::TYPE_ILIKE:
                return array($operation->getColumnName()=>array('$regex' => preg_quote($operation->getValue()), '$options' => 'i'));
            case Fetcher::TYPE_LIKE:
                return array($operation->getColumnName()=>array('$regex' => preg_quote($operation->getValue())));
            default :
                throw new \InvalidArgumentException(sprintf('Unsupported operation : %s !', $operation->getOperator()));
        }
    }
}
