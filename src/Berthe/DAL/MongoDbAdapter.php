<?php

namespace Berthe\DAL;

class MongoDbAdapter extends \Zend_Db_Adapter_Abstract
{
    /**
     * @var array
     */
    protected $_options = array(
        'connect' => true,
        'connectTimeoutMS' => 5000,
    );

    /**
     * @var \Mongo
     */
    protected $_connection;

    /**
     * @var array
     */
    protected $_config;

    /**
     * @var \MongoDB
     */
    protected $_db;

    /**
     * @var string
     */
    protected $_hostString;

    /**
     * @param array $config
     *
     * @return \Mongo
     */
    public function __construct($config)
    {
        $this->_checkRequiredOptions($config);
        $this->_config = $config;
        $this->_hostString = 'mongodb://'.$config['host'].':'.$config['port'];
    }

    public function query($query, $bind = array())
    {
        $this->_connect();
        return $this->_db->execute($query);
    }

    public function __call($fn, $args)
    {
        $this->_connect();
        if (method_exists($this->_db, $fn)) {
            return call_user_func_array(array($this->_db, $fn), $args);
        }

        throw new \Exception("MongoDB::{$fn} Method not found");
    }

    protected function _checkRequiredOptions(array $config)
    {
        if (!isset($config['host'])) {
            throw new \InvalidArgumentException(
                "Configuration array must have a key for 'host'"
            );
        }
        if (!isset($config['port'])) {
            throw new \InvalidArgumentException(
                "Configuration array must have a key for 'port'"
            );
        }
        if (!isset($config['options'])) {
            throw new \InvalidArgumentException(
                "Configuration array must have a key for 'options'"
            );
        }
    }

    /** Abstract methods implementations **/

    /**
     * Returns a list of the collections in the database.
     *
     * @return array
     */
    public function listTables()
    {
        return $this->_db->listCollections();
    }

    /**
     * @todo implement
     */
    public function describeTable($tableName, $schemaName = null)
    {
        throw new \BadMethodCallException('Not implemented yet');
    }

    /**
     * {@inheritdoc}
     */
    protected function _connect()
    {
        if (!$this->isConnected()) {
            $this->_connection = new \MongoClient(
                $this->_hostString,
                $this->_config['options']
            );
            $this->_connection->connect();
            $this->_db = $this->_connection->selectDb($this->_config['options']['db']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isConnected()
    {
        return ($this->_connection);
    }

    /**
     * {@inheritdoc}
     */
    public function closeConnection()
    {
        return $this->_connection->close();
    }

    public function prepare($sql)
    {
        throw new \BadMethodCallException('Cannot prepare statements in MongoDB');
    }

    public function lastInsertId($tableName = null, $primaryKey = null)
    {
        throw new \BadMethodCallException('Not implemented yet');
    }

    protected function _beginTransaction()
    {
        throw new \BadMethodCallException('There are no transactions in MongoDB');
    }

    protected function _commit()
    {
        throw new \BadMethodCallException('There are no commits(ie: transactions) in MongoDB');
    }

    protected function _rollBack()
    {
        throw new \BadMethodCallException('There are no rollbacks(ie: transactions) in MongoDB');
    }

    /**
     * @todo implement
     */
    public function setFetchMode($mode)
    {
        throw new \BadMethodCallException('Not implemented yet');
    }

    /**
     * @todo implement
     */
    public function limit($sql, $count, $offset = 0)
    {
        throw new \BadMethodCallException('Not implemented yet');
    }

    /**
     * @todo implement
     */
    public function supportsParameters($type)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getServerVersion()
    {
        return \MongoClient::VERSION;
    }
}
