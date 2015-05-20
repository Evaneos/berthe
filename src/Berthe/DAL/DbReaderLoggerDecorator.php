<?php

namespace Berthe\DAL;

use Berthe\Util\ParameterTransformer;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;

class DbReaderLoggerDecorator implements DbReader
{
    /**
     * @var DbReader
     */
    protected $reader;

    /**
     * @var Stopwatch
     */
    protected $stopWatch;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ParameterTransformer
     */
    protected $parameterTransformer;

    /**
     * @var string
     */
    protected $backendName;

    /**
     * @param DbReader             $reader
     * @param ParameterTransformer $parameterTransformer
     * @param string               $backendName
     */
    public function __construct(DbReader $reader, ParameterTransformer $parameterTransformer, $backendName)
    {
        $this->reader = $reader;
        $this->parameterTransformer = $parameterTransformer;
        $this->stopWatch = new Stopwatch();
        $this->logger = new NullLogger();
        $this->backendName = $backendName;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param Stopwatch $stopwatch
     */
    public function setStopwatch(Stopwatch $stopwatch)
    {
        $this->stopWatch = $stopwatch;
    }

    /**
     * @param string               $query
     * @param StopwatchEvent $event
     * @param string[]          $parameters
     * @param string               $action
     * @param string               $mode
     */
    protected function log($query, array $parameters, StopwatchEvent $event, $action, $mode)
    {
        $this->logger->debug($query, array(
            'execution_time' => $event->getDuration(),
            'real_memory_usage' => $event->getMemory(),
            'parameters' => $parameters,
            'action' => $action,
            'mode' => $mode,
            'backend_name' => $this->backendName
        ));
    }

    /**
     * @param string $query
     * @param array  $parameters
     * @param string $fetchMode
     *
     * @return array
     */
    public function fetchAll($query, array $parameters = array(), $fetchMode = null)
    {
        $parameters = $this->parameterTransformer->transform($parameters);

        $this->stopWatch->start('query', 'database');
        $result = $this->reader->fetchAll($query, $parameters, $fetchMode);
        $event = $this->stopWatch->stop('query');

        $this->log($query, $parameters, $event, 'fetch', 'all');

        return $result;
    }

    /**
     * @param string $query
     * @param array  $parameters
     *
     * @return mixed
     */
    public function fetchOne($query, array $parameters = array())
    {
        $parameters = $this->parameterTransformer->transform($parameters);

        $this->stopWatch->start('query', 'database');
        $result = $this->reader->fetchOne($query, $parameters);
        $event = $this->stopWatch->stop('query');

        $this->log($query, $parameters, $event, 'fetch', 'one');

        return $result;
    }

    /**
     * @param string $query
     * @param array  $parameters
     *
     * @return array
     */
    public function fetchAssoc($query, array $parameters = array())
    {
        $parameters = $this->parameterTransformer->transform($parameters);

        $this->stopWatch->start('query', 'database');
        $result = $this->reader->fetchAssoc($query, $parameters);
        $event = $this->stopWatch->stop('query');

        $this->log($query, $parameters, $event, 'fetch', 'assoc');

        return $result;
    }

    /**
     * @param string $query
     * @param array  $parameters
     *
     * @return array
     */
    public function fetchCol($query, array $parameters = array())
    {
        $parameters = $this->parameterTransformer->transform($parameters);

        $this->stopWatch->start('query', 'database');
        $result = $this->reader->fetchCol($query, $parameters);
        $event = $this->stopWatch->stop('query');

        $this->log($query, $parameters, $event, 'fetch', 'col');

        return $result;
    }

    /**
     * @param string $query
     * @param array  $parameters
     *
     * @return array
     */
    public function fetchPairs($query, array $parameters = array())
    {
        $parameters = $this->parameterTransformer->transform($parameters);

        $this->stopWatch->start('query', 'database');
        $result = $this->reader->fetchCol($query, $parameters);
        $event = $this->stopWatch->stop('query');

        $this->log($query, $parameters, $event, 'fetch', 'pairs');

        return $result;
    }

    /**
     *
     * @param string $query
     * @param array  $parameters
     * @param int    $fetchMode
     *
     * @return array
     */
    public function fetchRow($query, array $parameters = array(), $fetchMode = null)
    {
        $parameters = $this->parameterTransformer->transform($parameters);

        $this->stopWatch->start('query', 'database');
        $result = $this->reader->fetchRow($query, $parameters, $fetchMode);
        $event = $this->stopWatch->stop('query');

        $this->log($query, $parameters, $event, 'fetch', 'row');

        return $result;
    }

    /**
     * @param string $tableName
     * @param string $schemaName
     *
     * @return array
     */
    public function describeTable($tableName, $schemaName = null)
    {
        return $this->reader->describeTable($tableName, $schemaName);
    }
}
