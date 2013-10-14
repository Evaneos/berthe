<?php

class Berthe_Payment_Slimpay_SCIM {
    const REQUEST_TYPE_COLLECT   = 'collectPayment';
    const REQUEST_TYPE_SIGNATURE = '';
    
    const CLIENT_TYPE_INDIVIDUAL = 'individual';
    const CLIENT_TYPE_COMPANY    = 'company';
    /**
     *
     * @var Berthe_Modules_Transaction_VO
     */
    private $_transaction = null;
    /**
     *
     * @var Berthe_Modules_Quote_VO
     */
    private $_quote = null;
    /**
     *
     * @var string
     */
    private $_requestType = '';
    /**
     *
     * @var integer
     */
    private $_transactionId = 0;
    /**
     *
     * @var integer
     */
    private $_userId = 0;
    /**
     *
     * @var string
     */
    private $_clientType = '';
    /**
     *
     * @var string
     */
    private $_lastName = '';
    /**
     *
     * @var string
     */
    private $_email = '';
    /**
     *
     * @var DateTime
     */
    private $_debitExecutionDate = null;
    /**
     *
     * @var string
     */
    private $_debitLabel = '';
    /**
     *
     * @var float
     */
    private $_debitAmount = 0.0;
    /**
     *
     * @var string
     */
    private $_command  = '';
    /**
     *
     * @var boolean
     */
    private $_result = false;
    private $_addressLine1 = '38 rue du sentier';
    private $_addressCity = 'Paris';
    private $_addressZip = '75002';
    private $_addressCountry = 'FR';
    /**
     *
     * @var string
     */
    private $_blob = null;
    
    private function _makeCommand() {
        require 'Slimpay/tpe-php/include/common_php/variables.php';
        $this->_command = $PREFIX_COMMAND;
        $this->_command .= ' ' .
                '-requestType '        . '"' . $this->_requestType                         . '"' . ' ' .
                '-transactionId '      . '"' . $this->_transactionId                       . '"' . ' ' .
                '-clientReference '    . '"' . $this->_userId                              . '"' . ' ' .
                '-clientType '         . '"' . $this->_clientType                          . '"' . ' ' .
                '-contactLN '          . '"' . $this->_lastName                            . '"' . ' ' .
                '-contactFN '          . '"' . $this->_firstName                           . '"' . ' ' .
                '-contactEmail '       . '"' . $this->_email                               . '"' . ' ' .
                '-debitAmount '        . '"' . $this->_debitAmount                         . '"' . ' ' .
                '-debitExecutionDate ' . '"' . $this->_debitExecutionDate->format('Y-m-d') . '"' . ' ' .
                '-debitLabel '         . '"' . $this->_debitLabel                          . '"' . ' ' .
                '-Iline1 '             . '"' . $this->_addressLine1                        . '"' . ' ' .
                '-Icity '              . '"' . $this->_addressCity                         . '"' . ' ' .
                '-IpostalCode '        . '"' . $this->_addressZip                          . '"' . ' ' .
                '-Icountry '           . '"' . $this->_addressCountry                      . '"' . ' ' .
                '';
    }
    
    public function __construct() {
        $this->_requestType = self::REQUEST_TYPE_COLLECT;
        $this->_clientType = self::CLIENT_TYPE_INDIVIDUAL;
        $this->_debitExecutionDate = new DateTime();
    }
    
    public function setData(Berthe_Modules_Transaction_VO $_transaction, Berthe_Modules_User_VO $_user) {
        $this->_transaction    = $_transaction;
        $this->_transactionId  = $_transaction->id;
        $this->_email          = $_transaction->email;
        $this->_debitLabel     = $_transaction->label;
        $this->_debitAmount    = number_format($_transaction->amount, 2, '.', '');
        $this->_lastName       = $_user->lastname;
        $this->_firstName      = $_user->firstname;
        $this->_userId         = $_user->id;
        $this->_addressLine1   = $_user->address1;
        $this->_addressZip     = $_user->zip;
        $this->_addressCity    = $_user->city;
        $this->_addressCountry = $_user->country_address;
    }
    
    public function getResponse($blob) {
        require 'Slimpay/tpe-php/include/common_php/variables.php';
        $this->_command = $PREFIX_COMMAND . ' -response ' . $blob;
        $_result = shell_exec($this->_command);
        $_pairs = explode("&", $_result);
        $this->_result = array();
        foreach ($_pairs as $pair) {
                $pairArr = explode("=", $pair);
                $this->_result[$pairArr[0]] = $pairArr[1];
        }
        return $this->_result;
    }
    
    public function execute() {
        $_SESSION['transaction_id'] = $this->_transactionId;
        $this->_makeCommand();
        $_result = shell_exec($this->_command);
        $this->_result = ($_result and !strstr($_result, 'ERROR'));
        if(!$this->_result) {
            throw new RuntimeException('Can\'t make blob');
        }
        $this->_result and $this->_blob = $_result;
        return $this->_result;
    }
    
    public function getLastResult() {
        return $this->_result;
    }
    
    public function getLastBlob() {
        return $this->_blob;
    }
}