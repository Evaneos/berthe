<?php
    require_once 'Payline/lib/paylineSDK.php';
    require_once 'Payline/configuration/identification.php';
    require_once 'Payline/configuration/options.php';
    require_once 'Evaneos/Payline.php';

class Berthe_Payment_Payline extends Berthe_Payment_Abstract {

    /**
     * @var PaylineSDK 
     */
    protected $_payline = null;
    protected $_webPaymentConfig = array();
    protected $_paymentDetails = array();
    protected $_transactionManager = null;

    protected function _init() {
        $this->_payline = new paylineSDK();
    }

    protected function _getCurrencyCode($currency) {
        /* UNCOMMENT WHEN CURRENCIES MANAGER CREATED AND NEEDING OTHER CURRENCIES
          $_currency = Berthe_Currency_Manager::getInstance()->getById($this->_transaction->currencyId);
          return $_currency->paylineId;
         */
        return '978';
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
            case '01913' :
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
            case '01913' :
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
                return t('Contactez votre agent local afin qu\'il vous fournisse un nouveau lien de paiement.');
                break;
            case '01913' :
                return t('Contactez votre agent local afin qu\'il vous fournisse un nouveau lien de paiement. Cette erreur survient parce qu\'une erreur précédente est apparue (généralement un plafond dépassé). Contactez également votre banque.');
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

    protected function _processPayment() {
        $_amount    = (float)(int) ($this->_transaction->amount * 100);
        $_currency  = $this->_getCurrencyCode(null);//$this->_transaction->currencyId);
        $_ref       = $this->_transaction->id;
        $_name      = (!empty($this->_transaction->customer)) ? $this->_transaction->customer : 'Non renseigné';
        $_firstName = (!empty($this->_transaction->customer)) ? $this->_transaction->customer : 'Non renseigné';
        $_email     = $this->_email;
        //$_currency = Berthe_Currency_Manager::getInstance()->getById($this->_transaction->currencyId);
        // PAYMENT
        $this->_webPaymentConfig['payment']['amount'] = $_amount;
        $this->_webPaymentConfig['payment']['currency'] = $_currency;//$_currency);
        // ORDER
        $this->_webPaymentConfig['order']['ref'] = $_ref;
        $this->_webPaymentConfig['order']['amount'] = $_amount;
        $this->_webPaymentConfig['order']['currency'] = $_currency; // $_currency->code
        // BUYER
        $this->_webPaymentConfig['buyer']['lastName'] = $_name;
        $this->_webPaymentConfig['buyer']['firstName'] = $_firstName;
        $this->_webPaymentConfig['buyer']['email'] = $_email;
        $this->_webPaymentConfig['returnURL'] = BASE_URL . 'transaction/return/';
        $this->_webPaymentConfig['cancelURL'] = BASE_URL . 'transaction/cancel/';
        
        // EXECUTE
        $result = $this->_payline->do_webpayment($this->_webPaymentConfig);

        if (isset($result)) {
            $_transactionResult = $this->getStatusByReturnCode($result['result']['code']);
            $this->_transaction->details = $this->getErrorMessageByReturnCode($result['result']['code']);
            if ($_transactionResult === Berthe_Modules_Transaction_VO::STATUS_PAID) {
                $this->_transaction->status = Berthe_Modules_Transaction_VO::STATUS_PROCESSING;
                $this->_transactionManager->save($this->_transaction);
                HttpHelper::redirectUrl($result['redirectURL']);
                $_res = true;
            } else {
                $_res = array(
                    'error_code' => $result['result']['code'],
                    'error_message' => $result['result']['longMessage']
                );
                $this->_transaction->status = $_transactionResult;
            }
        } else {
            $this->_transaction->status = Berthe_Modules_Transaction_VO::STATUS_ERROR;
            $_res = array(
                'error_code' => $result['result']['code'],
                'error_message' => t('Une erreur est survenue lors de la transaction.')
            );
        }
        $this->_transactionManager->save($this->_transaction);
        return $_res;
    }
    
    public function getDetailsByToken($token) {
        if(!isset($this->_paymentDetails[$token])) {
            $this->_paymentDetails[$token] = $this->_payline->get_webPaymentDetails($token, array('version' => ''));
        }
        return $this->_paymentDetails[$token];
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

    public function getTransactionIdByToken($token) {
        /*
         * We need the evaneos transaction id given at the start of the payment process
         * To get that, we only have the token. This one will help us retrieving the
         * PAYLINE transaction id witch will help to get the transation details, this way
         * we will get the evaneos transaction id
         */
        // Get payment details
        $this->getDetailsByToken($token);
        if ($this->_paymentDetails[$token]) {
            if (!is_array($this->_paymentDetails[$token]) or count($this->_paymentDetails[$token]) == 0) {
                throw new Exception('Une erreur est suvenur lors de l\'obtention des détails du paiement');
                return false;
            }
            // Get payline side transaction id
            $_paylineTransactionId = (isset($this->_paymentDetails[$token]['transaction']['id'])) ? $this->_paymentDetails[$token]['transaction']['id'] : '';
            if (empty($_paylineTransactionId)) {
                throw new Exception('Id de transaction PAYLINE nulle');
                return false;
            }
            // Get transaction details
            $_paylineTransactionDetails = $this->_payline->get_TransactionDetails(array('transactionId' => $_paylineTransactionId, 'orderRef' => '', 'startDate' => '', 'endDate' => '', 'version' => '', 'Switch' => array('Choice' => 'Primaire', 'Force' => false)));
            if (!is_array($_paylineTransactionDetails) or count($_paylineTransactionDetails) == 0) {
                throw new Exception('Une erreur est suvenur lors de l\'obtention des détails de la transaction');
                return false;
            }
            // get transaction id
            $_transactionId = (isset($_paylineTransactionDetails['order']['ref'])) ? (int) $_paylineTransactionDetails['order']['ref'] : 0;
            if ($_transactionId <= 0) {
                throw new Exception('Id de transaction EVANEOS nulle');
                return false;
            } else {
                return $_transactionId;
            }
        }
    }

}