<?php

class Evaneos_Berthe_Payment_Factory {
    const MODE_TRANSFER   = 'transfer';
    const MODE_CARDONLINE = 'cardonline';
    /**
     *
     * @var Evaneos_Payment_Slimpay
     */
    protected static $_slimpayInstance = null;
    /**
     *
     * @var Evaneos_Payment_Payline
     */
    protected static $_paylineInstance = null;
    /**
     * Returns an instance or slimpay
     * @return Evaneos_Payment_Slimpay 
     */
    public static function getInstanceSlimpay() {
        is_null(self::$_slimpayInstance) and self::$_slimpayInstance = new Evaneos_Berthe_Payment_Slimpay();
        return self::$_slimpayInstance;
    }
    /**
     * Returns an instance of payline
     * @return Evaneos_Payment_Payline 
     */
    public static function getInstancePayline() {
        is_null(self::$_paylineInstance) and self::$_paylineInstance = new Evaneos_Berthe_Payment_Payline();
        return self::$_paylineInstance;
    }
    /**
     * Returns a payment mode depending of the mode selected
     * @param self::MODE_* $mode
     * @return Evaneos_Payment_Abstract
     * @throws Exception If invalid mode
     */
    public static function getInstanceByMode($mode) {
        switch($mode) {
            case self::MODE_TRANSFER :
                return self::getInstanceSlimpay();
                break;
            case self::MODE_CARDONLINE :
                return self::getInstancePayline();
                break;
            default :
                throw new Exception('Invalid payment mode');
                return null;
                break;
        }
    }
}
