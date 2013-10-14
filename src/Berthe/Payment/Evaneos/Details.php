<?php

class Berthe_Payment_Evaneos_Details extends Berthe_Payment_Details {
    public function setDetails($details) {
        $this->_transactionId         = (isset($details['transaction_id']) ? (int)$details['transaction_id'] : null);
        $this->_mode                  = (isset($details['mode']) ? $details['mode'] : null);
        $this->_providerTransactionId = 'EVANEOS_' . $this->_transactionId;
        $this->_success               = ((bool)$this->_transactionId and !is_null($this->_mode));
        $this->_status                = ($this->_success) ? Berthe_Modules_Transaction_VO::STATUS_PAID : Berthe_Modules_Transaction_VO::STATUS_ERROR;
        $this->_error                 = ($this->_success) ? self::ERROR_NONE : self::ERROR_EVANEOS;
    }
}