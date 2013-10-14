<?php

class Berthe_Payment_Payline_Details extends Berthe_Payment_Details {
    public function setDetails($details) {
        $this->_mode = Berthe_Modules_Transaction_VO::MODE_CARD_ONLINE;
        $this->_providerTransactionId = isset($details['transaction']['id']) ? $details['transaction']['id'] : '';
        $this->_success = false;
        switch ($details['result']['code']) {
            case '00000' :
            case '01001' :
                $this->_status = Berthe_Modules_Transaction_VO::STATUS_PAID;
                $this->_success = true;
                $this->_error = self::ERROR_NONE;
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
                $this->_status = Berthe_Modules_Transaction_VO::STATUS_DO_NOT_HONOR;
                $this->_errorHR = t('Le plafond de votre carte bancaire a été atteint');
                $this->_howTo = t('Contactez votre banque afin qu\'elle le débloque temporairement.');
                $this->_error = self::ERROR_CUSTOMERAMOUNT;
                break;
            case '01109' :
            case '01110' :
            case '01116' :
                $this->_status = Berthe_Modules_Transaction_VO::STATUS_INVALID;
                $this->_errorHR = t('Le montant de la transaction n\'est pas valide');
                $this->_howTo = t('Contactez votre agent local afon qu\'il vous fournisse un nouveau lien de paiement.');
                $this->_error = self::ERROR_INVALIDTRANSACTION;
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
                $this->_status = Berthe_Modules_Transaction_VO::STATUS_CARD_ERROR;
                $this->_errorHR = t('Des informations erronées ont été remplies');
                $this->_howTo = t('Vérifiez la les données de votre carte bancaire');
                $this->_error = self::ERROR_MANIPULATION;
                break;
            case '01208' :
            case '01209' :
                $this->_status = Berthe_Modules_Transaction_VO::STATUS_INVALID_CARD;
                $this->_errorHR = t('Votre carte à été désactivée');
                $this->_howTo = t('Utilisez une carte valide.');
                $this->_error = self::ERROR_MANIPULATION;
                break;
            case '02301':
            case '02302':
            case '02303':
            case '02304':
            case '02305':
            case '02317':
            case '02318':
            case '02319':
                $this->_status = Berthe_Modules_Transaction_VO::STATUS_INVALID;
                $this->_errorHR = t('La transaction existe déjà');
                $this->_howTo = t('Contactez votre agent local afon qu\'il vous fournisse un nouveau lien de paiement.');
                $this->_error = self::ERROR_INVALIDTRANSACTION;
                break;
            case '02101':
                $this->_status = Berthe_Modules_Transaction_VO::STATUS_ERROR;
                $this->_errorHR = t('Une erreur interne est survenue');
                $this->_howTo = t('Contactez votre agent local afon qu\'il vous fournisse un nouveau lien de paiement.');
                $this->_error = self::ERROR_PROVIDER;
                break;
            default:
                $this->_status = Berthe_Modules_Transaction_VO::STATUS_REFUSED;
                $this->_errorHR = t('Le paiement à été refusé');
                $this->_howTo = t('Contactez votre banque afin qu\'elle le débloque temporairement.');
                $this->_error = self::ERROR_CUSTOMERAMOUNT;
                break;
        }
    }
}