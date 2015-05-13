<?php

namespace Berthe\DAL;

class DbReader extends DbAdapter
{

    protected $sanitizers = array();

    /**
     * @param string $sql
     * @param array $array
     * @param string $fetchMode
     * @return array
     */
    public function fetchAll($sql, array $array = array(), $fetchMode = null)
    {
        return $this->db->fetchAll($sql, $this->sanitizeBinds($array), $fetchMode);
    }

    /**
     *
     * @param string $sql
     * @param array $array
     * @return mixed
     */
    public function fetchOne($sql, array $array = array())
    {
        return $this->db->fetchOne($sql, $this->sanitizeBinds($array));
    }

    /**
     *
     * @param string $sql
     * @param array $array
     * @return array
     */
    public function fetchAssoc($sql, array $array = array())
    {
        return $this->db->fetchAssoc($sql, $this->sanitizeBinds($array));
    }

    /**
     *
     * @param string $sql
     * @param array $array
     * @return array
     */
    public function fetchCol($sql, array $array = array())
    {
        return $this->db->fetchCol($sql, $this->sanitizeBinds($array));
    }

    /**
     *
     * @param string $sql
     * @param array $array
     * @return array
     */
    public function fetchPairs($sql, array $array = array())
    {
        return $this->db->fetchPairs($sql, $this->sanitizeBinds($array));
    }

    /**
     *
     * @param string $sql
     * @param array $array
     * @param int $fetchMode
     * @return array
     */
    public function fetchRow($sql, array $array = array(), $fetchMode = null)
    {
        return $this->db->fetchRow($sql, $this->sanitizeBinds($array), $fetchMode);
    }

    /**
     *
     * @param string $tableName
     * @param string $schemaName
     * @return array
     */
    public function describeTable($tableName, $schemaName = null)
    {
        return $this->db->describeTable($tableName, $schemaName);
    }

    /**
     * Adds a sanitaizer
     *
     * @param string $typeName
     * @param string $callable
     * @throws \InvalidArgumentException
     */
    public function addSanitizer($typeName, $callable)
    {
        if (! is_callable($callable)) {
            throw new \InvalidArgumentException('$callable is not a callable.');
        }

        $this->sanitizers[$typeName] = $callable;
    }

    /**
     * Sanitize binds
     * @param array $bind
     * @return array
     */
    protected function sanitizeBinds(array $bind = array())
    {
        $sanitizedBinds = array();
        foreach ($bind as $key => $value) {
            if (is_object($value) && array_key_exists(get_class($value), $this->sanitizers)) {
                $sanitizer = $this->sanitizers[get_class($value)];
                $sanitizedValue = call_user_func($sanitizer, $value);
            } elseif (array_key_exists(gettype($value), $this->sanitizers)) {
                $sanitizer = $this->sanitizers[gettype($value)];
                $sanitizedValue = call_user_func($sanitizer, $value);
            } else {
                switch (1) {
                    case ($value instanceof \Berthe\Translation\Translation) :
                        $sanitizedValue = $value->getId();
                        break;
                    case ($value instanceof \DateTime) :
                        $sanitizedValue = $value->format('Y-m-d H:i:s');
                        break;
                    case is_bool($value) :
                        $sanitizedValue = (int)$value;
                        break;
                    case is_string($value) :
                        $sanitizedValue = mb_check_encoding($value, 'UTF-8') ? $value : utf8_encode($value);
                    default :
                        $sanitizedValue = $value;
                        break;
                }
            }
            $sanitizedBinds[$key] = $sanitizedValue;
        }
        return $sanitizedBinds;
    }

    /**
     * @param  array  $values
     * @return string[] params names to use
     */
    protected function transformToBinds(array $values = array())
    {
        $binds = array();
        $sprintfArgs = array();

        foreach ($values as $key => $value) {
            if ($value instanceof \Berthe\DAL\ComplexType) {
                list($query, $params) = $value->toDbRepresentation();
                $args = array();
                foreach ($params as $paramKey => $paramValue) {
                    $paramName = $key . '_' . $paramKey;
                    $args[] = ':' . $paramName;
                    $binds[':' . $paramName] = $paramValue;
                }
                $sprintfArgs[] = vsprintf($query, $args);
            } else {
                $sprintfArgs[] = ':' . $key;
                $binds[':' . $key] = $value;
            }
        }

        return array(
            'sprintfArgs' => $sprintfArgs,
            'binds' => $binds
        );
    }
}
