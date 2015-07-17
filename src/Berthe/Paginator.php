<?php
namespace Berthe;

class Paginator implements \ArrayAccess
{
    /**
     * Filters set
     *
     * @var array
     */
    protected $_elements = array();

    /** @var int */
    protected $_page = 1;

    /** @var int */
    protected $_nbByPage = 25;

    /** @var int */
    protected $_ttlCount = 0;

    /** @var int */
    protected $_count = 0;

    /** @var string|null */
    protected $_keyGetter = null;

    /**
     * Checks if an offset exists
     *
     * @param string $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->_elements[$offset]);
    }

    /**
     * Gets an offset
     *
     * @param int $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return is_int($offset) ? $this->_elements[$offset] : null;
    }

    /**
     * sets an offset
     *
     * @param int $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        !is_int($offset) and trigger_error(
            __CLASS__ . '::' . __FUNCTION__ . '() : Only accepts integer offsets',
            E_USER_WARNING
        );
        ($this->_count >= $this->_nbByPage) and trigger_error(
            __CLASS__ . '::' . __FUNCTION__ . '() : Max number of elements by page reached',
            E_USER_ERROR
        );
        if (is_int($offset) and ($this->hasLimit() && ($this->_count >= $this->_nbByPage))) {
            $this->_elements[$offset] = $value;
            $this->_count = count($this->_elements);
        }
    }

    /**
     * Unsets an offset
     *
     * @param int $offset
     */
    public function offsetUnset($offset)
    {
        !is_int($offset) and trigger_error(
            __CLASS__ . '::' . __FUNCTION__ . '() : Only accepts integer offsets',
            E_USER_WARNING
        );
        if (is_int($offset) and isset($this->_elements[$offset])) {
            unset($this->_elements[$offset]);
            $this->_count = count($this->_elements);
        }
    }

    /**
     * Sets the datas of $array to the elements
     *
     * @param array $array
     * @param boolean $preserveIds
     * @return false|void
     */
    public function set(array $array = array(), $preserveIds = false)
    {
        if ($this->hasLimit() and ($this->_nbByPage >= 0 and count($array) > $this->_nbByPage)) {
            trigger_error(
                __CLASS__ . '::' . __FUNCTION__ . '() : Max number of elements by page reached',
                E_USER_ERROR
            );
            return false;
        }

        if ($preserveIds) {
            if ($this->_keyGetter !== null) {
                $elementsWithNewKey = array();
                foreach ($array as $element) {
                    $key = $element->{$this->_keyGetter}();
                    $elementsWithNewKey[$key] = $element;
                }
                $this->_elements = $elementsWithNewKey;
            } else {
                $this->_elements = $array;
            }
        } else {
            $this->_elements = array_values($array);
        }

        $this->_count = count($this->_elements);
    }

    /**
     * Constructor
     *
     * @param integer $page
     * @param integer $nbByPage
     * @param array   $elements
     */
    public function __construct($page = 1, $nbByPage = 25, array $elements = array())
    {
        $this->_page = $page;
        $this->_nbByPage = $nbByPage;
        $this->set($elements);
    }

    /**
     * @return bool
     */
    public function hasLimit()
    {
        return ($this->_page >= 0 || $this->_nbByPage >= 0);
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->_page;
    }

    /**
     * @param int $pageNumber
     */
    public function setPage($pageNumber)
    {
        $this->_page = $pageNumber;
    }

    /**
     * @return int
     */
    public function getNbByPage()
    {
        return $this->_nbByPage;
    }

    /**
     * @param int $nbByPage
     */
    public function setNbByPage($nbByPage)
    {
        $this->_nbByPage = $nbByPage;
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->_count;
    }

    public function clear()
    {
        $this->set(array());
    }

    /**
     * @return bool
     */
    public function hasResults()
    {
        return $this->_count !== 0;
    }

    /**
     * @return VO[]|array
     */
    public function getResultSet()
    {
        if (reset($this->_elements) instanceof VO) {
            $res = array();
            foreach ($this->_elements as $key => $value) {
                /* @var $value VO */
                $key = $this->_keyGetter !== null ? $value->{$this->_keyGetter}() : $value->getId();
                $res[$key] = $value;
            }
            return $res;
        } else {
            return $this->_elements;
        }
    }

    /**
     * @return int
     */
    public function getTtlCount()
    {
        return $this->_ttlCount;
    }

    /**
     * @param int $ttlCount
     */
    public function setTtlCount($ttlCount)
    {
        if ($ttlCount >= $this->count()) {
            $this->_ttlCount = (int)$ttlCount;
        } else {
            $this->_ttlCount = $this->count();
        }
    }

    /**
     * @return float
     */
    public function getNbPages()
    {
        return ceil((float)$this->_ttlCount / (float)$this->_nbByPage);
    }

    /**
     * @param string|null $keyGetter
     */
    public function setKeyGetter($keyGetter)
    {
        $this->_keyGetter = $keyGetter;
    }
}
