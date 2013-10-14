<?php

class Berthe_Payment_Slimpay_Details extends Berthe_Payment_Details {
    public function setDetails($details) {
        $this->_mode = Berthe_Modules_Transaction_VO::MODE_TRANSFER;
        $_scim = new Berthe_Payment_Slimpay_SCIM();
        $_result = $_scim->getResponse($details['blob']);
        $this->_success = ($_result['transactionStatus'] == 'success');
        $_errorCode = (int)$_result['transactionErrorCode'];
        if($this->_success) {
            $this->_status = Berthe_Modules_Transaction_VO::STATUS_PAID;
        } else {
            $this->_status = Berthe_Modules_Transaction_VO::STATUS_ERROR;
        }
        if($_errorCode >= 100) {
            $this->_error = self::ERROR_PROVIDER;
            $this->_errorHR = t('Une erreur technique est survenue');
            $this->_howTo = t('Patientez quelques heures puis contactez votre agent pour obtenir un nouveau lien de paiement.');
            $this->_status = Berthe_Modules_Transaction_VO::STATUS_ERROR_PAYLINE;
        }
        $_signatureCode = (int)$_result['signatureOperationResult'];
        if($_signatureCode == 200) {
            $this->_error = self::ERROR_MANIPULATION;
            $this->_errorHR = t('Données incorrectes');
            $this->_howTo = t('Veuillez vérifier les données de votre RIB.');
            $this->_status = Berthe_Modules_Transaction_VO::STATUS_ERROR;
        }
        if($_signatureCode == 201) {
            $this->_error = self::ERROR_MANIPULATION;
            $this->_errorHR = t('Echec de la signature du mandat');
            $this->_howTo = t('Veuillez vérifier les données de votre RIB. Si le problème persiste contactez votre banque.');
            $this->_status = Berthe_Modules_Transaction_VO::STATUS_ERROR;
        }
        if($_signatureCode == 202) {
            $this->_error = self::ERROR_MANIPULATION;
            $this->_errorHR = t('Code SMS incorect');
            $this->_howTo = t('Le code SMS est incorect.');
            $this->_status = Berthe_Modules_Transaction_VO::STATUS_ERROR;
        }
        if($_signatureCode == 205) {
            $this->_error = self::ERROR_MANIPULATION;
            $this->_errorHR = t('Données incorrectes');
            $this->_howTo = t('Veuillez vérifier les données de votre RIB.');
            $this->_status = Berthe_Modules_Transaction_VO::STATUS_ERROR;
        }
        if($_signatureCode == 219) {
            $this->_error = self::ERROR_MANIPULATION;
            $this->_errorHR = t('Vous avez annulé la procédure');
            $this->_howTo = '';
            $this->_status = Berthe_Modules_Transaction_VO::STATUS_CANCELLED;
        }
        if($_signatureCode == 220) {
            $this->_error = self::ERROR_MANIPULATION;
            $this->_errorHR = t('Une erreur technique est survenue');
            $this->_howTo = t('Réessayez le paiement.');
            $this->_status = Berthe_Modules_Transaction_VO::STATUS_ERROR;
        }
        $_paymentErrorCode = (int)$_result['signatureOperationResult'];
        if($_paymentErrorCode == 300) {
            $this->_error = self::ERROR_MANIPULATION;
            $this->_errorHR = t('Données incorrectes');
            $this->_howTo = t('Veuillez vérifier les données de votre RIB.');
            $this->_status = Berthe_Modules_Transaction_VO::STATUS_ERROR;
        }
        if($_paymentErrorCode == 301 or $_paymentErrorCode == 305) {
            $this->_error = self::ERROR_MANIPULATION;
            $this->_errorHR = t('Données incorrectes');
            $this->_howTo = t('Veuillez vérifier les données de votre RIB.');
            $this->_status = Berthe_Modules_Transaction_VO::STATUS_ERROR;
        }
        if($_paymentErrorCode == 302) {
            $this->_error = self::ERROR_CUSTOMERAMOUNT;
            $this->_errorHR = t('Le montant maximal autorisé pour le prélèvement a été dépassé.');
            $this->_howTo = t('Veuillez contacter votre banque pour augmenter provisoirement votre plafond de prélèvement.');
            $this->_status = Berthe_Modules_Transaction_VO::STATUS_REFUSED;
        }
        if($_paymentErrorCode == 303 or $_paymentErrorCode == 305) {
            $this->_error = self::ERROR_MANIPULATION;
            $this->_errorHR = t('Débit interdit');
            $this->_howTo = t('Veuillez contacter votre banque pour autoriser le débit.');
            $this->_status = Berthe_Modules_Transaction_VO::STATUS_REFUSED;
        }
        if($_paymentErrorCode == 319) {
            $this->_error = self::ERROR_MANIPULATION;
            $this->_errorHR = t('Vous avez annulé la procédure');
            $this->_howTo = '';
            $this->_status = Berthe_Modules_Transaction_VO::STATUS_CANCELLED;
        }
        if($_paymentErrorCode == 320) {
            $this->_error = self::ERROR_MANIPULATION;
            $this->_errorHR = t('Une erreur technique est survenue');
            $this->_howTo = t('Réessayez le paiement.');
            $this->_status = Berthe_Modules_Transaction_VO::STATUS_ERROR;
        }
        
        $this->_transactionId = (int)$_result['transactionId'];
        $this->_providerTransactionId = 'SLMP' . $this->_transactionId;
     }
}