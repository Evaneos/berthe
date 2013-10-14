<?php

abstract class Berthe_Unit_AbstractData implements ArrayAccess {
    
    protected $_voClass = 'Berthe_Unit';

    protected $_data = array();
    
    protected static $_names = array(
        'AIMELAFILLE','ALLO','AMEN','ANONYME','AUREVOIR','BEAUF','BELLEMERE','BIBRON','BIZOUS','BOBO','BOUCHON','BOULON','BROUTECHOUX','CASSOULET','CASTAGNE','CENDRIER','CERVEAU','CHALOM','CHAPEAU','CHAUDOREILLE','CHAUVE','COEUR DE VACHE','COEURDACIER','CUCU-BALON','CURE','DELATRONCHETTE','DIAMANT','DODO','DOMMAGE','DUCONGE','EXPERT','FESSE','FEUILLEMORTE','FROMAGE','GENIALE','GROS BIDET','GROSSETETE','GRUYERE','JAMBON	JUSTICE','KELBOGOSSE','MACHIN','MADAME','MALOKRANE','MALPARTI','MAMAN','METRO','MEURDESOIF','MOLARD','MONSIEUR','MONTINTIN','NATIF','NE','NEE','NOMDEDIEU','NOURRISSON','NUL','NULNEMACHETE','OUBLIE','OUI','OVAIRE','PABIEN','PAPA','PAPI','PARADIS','PAYEBIEN','PLUMARD','POIRCUITTE','POPOT','PORNOT','POTAUFEU','POTDEVIN','POUBELLE','PURSEIGLE','QUATREBOEUFS','QUATREMERE','QUATRENOIX','QUATRESOUS	QUATREVIEUX','QUINZEBILLES','RABAJOIE','RADIS','REGARDENBAS','REVEILLECHIEN','RIGOLOLO','ROBINET','ROCFORT','ROGNON','ROSE','ROTI','SACAVIN','SAINTESPRIX','SAINT-NEANT','SANSFACON','SANSPEUR','SANSPOIL','SANSREGRET','SANSREPPROCHE','SOLEIL ','TATA','TETE','TORCHEBOEUF','TOURTE','TRIPE','TRIPOTIN','TROISOEUF','TROIS-POUX','TROUVE','UNIVERSEL ','VAN RATE','VAOUILMEPLAIT','VATAN','VIEILLEDENT','VIENS','VOICI','YGNARD'
    );
    
    protected static $_firstNames = array(
        'Ambroise','Amédée','Anastase','Arthur','Augustin','Aymeric','Béranger','Geoffroy','Grégoire','Guillaume','Léon','Louis','Théodore','Thibaut','Tristan',
        'Adélaïde','Adèle','Agnès','Alix','Béatrice','Beatrix','Elizabeth','Hélène','Héloïse','Isabeau','Iseult','Irène','Mahaut','Margot','Mathilde','Mélissende','Pétronille','Yolande'
    );
    
    abstract public function getInstance();

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset) {
        return isset($this->$_data[$offset]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset) {
        if(!isset($this->$_data[$offset])) {
            $_vo = $this->generateRandomObject($offset);
        } else {
            $_vo = $_data[$offset];
        }
        return $_vo;
    }

    /**
     * Sets a VO is the datas
     * @param mixed                     $offset Not used','the id of the vo is used as key','so assignation should be like : $_data[] = $vo;
     * @param Berthe_AbstractVO $value  The VO to set
     * 
     * @return void
     */
    public function offsetSet($offset, $value) {
        if($this->_voClass == 'Berthe_Unit') {
            throw new Exception('Class ' . get_class($this) . ' wrongly definied','$this->_voClass no defined.');
            return false;
        }
        if(get_class($value) == $this->_voClass) {
            $this->_data[$value->id] = $value;
        } else {
            throw new Exception('Wrong type of value');
        }
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset) {
        unset($this->_data[$offset]);
    }
    
    /**
     * Generates and returns a randomly created vo
     * @param integer [OPTIONAL] id','if defined should force the id
     * @return Berthe_AbstractVO
     */
    abstract public function generateRandomObject($id = null);
    
    /**
     * Returns a random vo
     * @return type 
     */
    public function getRandom() {
        $_keys = array_keys($this->_data);
        return $this->_data[$_keys[rand(0, count($_keys) -1)]];
    }
    /**
     * Generates a random name
     * @return string
     */
    protected function _getRandomName() {
        return self::$_names[rand(0, count(self::$_names) - 1)];
    }
    /**
     * Generates a random name
     * @return string
     */
    protected function _getRandomFirstName() {
        return self::$_names[rand(0, count(self::$_names) - 1)];
    }
    
    /**
     * Generates a random id.
     * /!\ can be very loud if numerous ids are asked in the same script
     * @param integer $tries [OPTIONAL] used in the recursivity stack
     * @return integer
     */
    protected function _generateId($tries = 0) {
        $_id =  rand(0, ((int)($tries / 300)) + 300);
        $tries++;
        if(isset($this->data[$_id])) {
            $_id = $this->generateId();
        }
        return $_id;
    }

}