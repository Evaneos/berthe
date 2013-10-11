<?php

class Evaneos_Berthe_Payment_Slimpay extends Evaneos_Berthe_Payment_Abstract {
    const ERROR_NOTLOGGED      = 1;
    const ERROR_DIFFERENT_MAIL = 2;
    
    /**
     *
     * @var Evaneos_Berthe_Payment_Slimpay_SCIM 
     */
    protected $_scim = null;
    /**
     *
     * @var string
     */
    protected $_slimUrl = '';
    /**
     *
     * @var Evaneos_Berthe_Payment_Slimpay_Properties 
     */
    protected $_properties = null;
    /**
     *
     * @var HttpRequest
     */
    protected $_restRequest = null;
    protected $_address = array();
    
    protected function _init() {
        require 'Slimpay/tpe-php/include/common_php/variables.php';
        $this->_scim = new Evaneos_Berthe_Payment_Slimpay_SCIM();

        $this->_properties = new Evaneos_Berthe_Payment_Slimpay_Properties($PROPERTIES_FILE_PATH);
        $mode = $this->_properties->getValue($CLIENT_PRODUCTION_FLAG);
        if ($mode == "true") {
            $this->_slimUrl = stripslashes($this->_properties->getValue($SERVER_CALL_URL_PROD));
        } else {
            $this->_slimUrl = stripslashes($this->_properties->getValue($SERVER_CALL_URL_DEV));
        }
    }
    /**
     * Return status by payline code
     * 
     * @param string $code
     * @return integer 
     */
    public function getStatusByReturnCode($code) {
        switch ($code) {
            case '00000' :
            case '01001' :
                return Berthe_Modules_Transaction_VO::STATUS_PAID;
                break;
            case '01100' :
            case '01103' :
            case '01108' :
            case '01113' :
            case '01119' :
            case '01120' :
            case '01121' :
            case '01122' :
            case '01123' :
            case '01200' :
            case '01206' :
                return Berthe_Modules_Transaction_VO::STATUS_DO_NOT_HONOR;
                break;
            case '01109' :
            case '01110' :
            case '01116' :
                return Berthe_Modules_Transaction_VO::STATUS_INVALID;
                break;
            case '01111' :
            case '01117' :
            case '01118' :
            case '01125' :
            case '01126' :
            case '01127' :
            case '01128' :
            case '01129' :
            case '01130' :
            case '01201' :
                return Berthe_Modules_Transaction_VO::STATUS_CARD_ERROR;
                break;
            case '01208' :
            case '01209' :
                return Berthe_Modules_Transaction_VO::STATUS_INVALID_CARD;
                break;
            case '02301':
            case '02302':
            case '02303':
            case '02304':
            case '02305':
            case '02317':
            case '02318':
            case '02319':
                return Berthe_Modules_Transaction_VO::STATUS_INVALID;
                break;
            case '02101':
                return Berthe_Modules_Transaction_VO::STATUS_ERROR;
                break;
            default:
                return Berthe_Modules_Transaction_VO::STATUS_REFUSED;
                break;
        }
    }
    
    public function getErrorMessageByReturnCode($code) {
        switch ($code) {
            case '00000' :
            case '01001' :
                return '';
                break;
            case '01100' :
            case '01103' :
            case '01108' :
            case '01113' :
            case '01119' :
            case '01120' :
            case '01121' :
            case '01122' :
            case '01123' :
            case '01200' :
            case '01206' :
                return t('Le plafond de votre carte bancaire a été atteint');
                break;
            case '01109' :
            case '01110' :
            case '01116' :
                return t('Le montant de la transaction n\'est pas valide');
                break;
            case '01111' :
            case '01117' :
            case '01118' :
            case '01125' :
            case '01126' :
            case '01127' :
            case '01128' :
            case '01129' :
            case '01130' :
            case '01201' :
                return t('Des informations erronées ont été remplies');
                break;
            case '01208' :
            case '01209' :
                return t('Votre carte à été désactivée');
                break;
            case '02301':
            case '02302':
            case '02303':
            case '02304':
            case '02305':
            case '02317':
            case '02318':
            case '02319':
                return t('La transaction existe déjà');
                break;
            case '02101':
                return t('Une erreur interne est survenue');
                break;
            default:
                return t('Le paiement à été refusé');
                break;
        }
    }
    
    public function getErrorInfosByReturnCode($code) {
        switch ($code) {
            case '00000' :
            case '01001' :
                return '';
                break;
            case '01100' :
            case '01103' :
            case '01108' :
            case '01113' :
            case '01119' :
            case '01120' :
            case '01121' :
            case '01122' :
            case '01123' :
            case '01200' :
            case '01206' :
                return t('Contactez votre banque afin qu\'elle le débloque temporairement.');
                break;
            case '01109' :
            case '01110' :
            case '01116' :
                return t('Contactez votre agent local afon qu\'il vous fournisse un nouveau lien de paiement.');
                break;
            case '01111' :
            case '01117' :
            case '01118' :
            case '01125' :
            case '01126' :
            case '01127' :
            case '01128' :
            case '01129' :
            case '01130' :
            case '01201' :
                return t('Vérifiez la les données de votre carte bancaire');
                break;
            case '01208' :
            case '01209' :
                return t('Utilisez une carte valide.');
                break;
            case '02301':
            case '02302':
            case '02303':
            case '02304':
            case '02305':
            case '02317':
            case '02318':
            case '02319':
            case '02101':
                return t('Contactez votre agent local afon qu\'il vous fournisse un nouveau lien de paiement.');
                break;
            default:
                return t('Contactez votre banque afin qu\'elle le débloque temporairement.');
                break;
        }
    }
    
    protected function _onTransactionSet() {
        $this->_scim->setData($this->_transaction, Berthe_Context::getInstance()->getUser());
    }

    protected function _processPayment() {
        $_ret = $this->_scim->execute();
        $_ret and $_blob = $this->_scim->getLastBlob();
        $_ret and $_res = <<<EOL
<?xml version="1.0" encoding="UTF-8"?>
<html>
    <header></header>
    <body onload="document.forms['mainForm'].submit()">
        <form action="$this->_slimUrl" method="post" name="mainForm"> 
            <input type="hidden" name="blob" value="$_blob"/>
            <noscript>
            <input type="submit" name="envoyer" value="envoyer" />
            </noscript>
        </form>
    </body>
</html> 
EOL;
        $_ret and die($_res);
        return $_ret;
    }
    
    protected function _getMessageByErrorCode($code) {
        switch($code) {
            case self::CONST_ERROR_SLIMPAY + self::ERROR_NOTLOGGED :
                return t('Pour des raisons de sécurité, vous devez vous identifier pour effectuer un paiement par prélèvement bancaire.');
            case self::CONST_ERROR_SLIMPAY + self::ERROR_DIFFERENT_MAIL :
                return t('Pour des raisons de sécurité, vous devez vous identifier au compte correspondant au mail que vous avez associé au paiement pour effectuer un paiement par prélèvement bancaire.');
            default :
                return t('Erreur Technique');
                    
        }
    }
    
    protected function _onValidatePayment() {
        $_context = Berthe_Context::getInstance();
        // check user is logged
        if(!$_context->isUserLogged()) {
            $_error = self::CONST_ERROR_SLIMPAY + self::ERROR_NOTLOGGED;
            $_data  = array(
                'transaction' => $this->_transaction
            );
            $this->_errorHandler->add($this->_getMessageByErrorCode($_error), $_error, $_data);
            return false;
        }
        // check emails are the same
        if(strtolower($this->_transaction->email) != strtolower($_context->getUser()->email)) {
            $_error = self::CONST_ERROR_SLIMPAY + self::ERROR_DIFFERENT_MAIL;
            $_data  = array(
                'transaction' => $this->_transaction,
                'user_email'  => $_context->getUser()->email,
            );
            $this->_errorHandler->add($this->_getMessageByErrorCode($_error), $_error, $_data);
            return false;
        }
        return true;
    }
    
    public function getTransactionDetailsByTransaction(Berthe_Modules_Transaction_VO $transaction) {
        if(!isset($this->_transactionDetails[$transaction->id])) {
            $this->_transactionDetails[$transaction->id] = $this->_payline->get_TransactionDetails(array(
                'version' => '',
                'transactionId' => $transaction->transactionId,
                'orderRef' => $transaction->id,
                'startDate' => '',
                'endDate' => '',
                'contractNumber' => PAYINE_API_CONTRACT,
                'paymentRecordId' => $transaction->transactionId
            ));
        }
        return $this->_transactionDetails[$transaction->id];
    }

}