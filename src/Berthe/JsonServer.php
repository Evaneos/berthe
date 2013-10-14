<?php

class Berthe_JsonServer {
    const ERROR_CODE_INVALID_REQUEST = 1;
    const ERROR_CODE_INVALID_SERVICE = 2;
    const ERROR_CODE_INVALID_METHOD  = 4;
    const ERROR_CODE_WRONG_REQUEST   = 8;
    
    const METHOD_ERROR_CODE_EXCEPTION    = 1;
    const METHOD_ERROR_CODE_UNAUTHORIZED = 2;
    const METHOD_ERROR_LOGIC_EXCEPTION   = 4;
    const METHOD_ERROR_CODE_SKIPPED      = 8;
    
    const ERROR_MESSAGE_INVALID_REQUEST = 'Invalid request specified';
    const ERROR_MESSAGE_INVALID_SERVICE = 'Invalid service class';
    const ERROR_MESSAGE_INVALID_METHOD  = 'Invalid methode name';
    const ERROR_MESSAGE_WRONG_REQUEST   = 'Error on method call';
    const ERROR_MESSAGE_SKIPPED_REQUEST = 'Request skipped due to previous error';
    
    const METHOD_ERROR_MESSAGE_EXCEPTION    = '';
    const METHOD_ERROR_MESSAGE_UNAUTHORIZED = 'Vous n\'avez pas l\'autorisation d\'exécuter cette action.';
    
    private static $_errors = array();
    
    private $_requests = array();
    private $_result = array();
    private $_success = true;
    private $_error = array(
                'code'    => 0,
                'message' => '',
                'data'    => null
    );
    
    private $_services = array();
    
    private $_factoryService = null;
    private $_factoryServiceXhr = null;
    
    public function __construct(array $requests = array(), Berthe_Context $context = null) {
        $this->_factoryService = Berthe_FactoryService::getInstance($context);
        $this->_factoryServiceXhr = new Berthe_FactoryServiceXhr($context);
        
        foreach ($requests as $key => $value) {
            if(isset($value['service']) and isset($value['method']) and isset($value['id'])) {
                $this->_requests[$value['id']] = $value;
            } else {
                $this->_success = false;
                $this->_error = array(
                    'code'    => self::ERROR_CODE_INVALID_REQUEST,
                    'message' => self::ERROR_MESSAGE_INVALID_REQUEST,
                    'data'    => $value
                );
                return;
            }
        };
    }
    
    public function handle() {
        if($this->_success) {
            $this->_result = array();
            foreach ($this->_requests as $key => $value) {
                if ($this->_success) {
                    $this->_result[$key] = $this->_handleRequest($value);
                }
                else {
                    $this->_result[$key] = $this->_skipRequest($value);
                }
            }
        }
        return $this->_makeResult();
    }
    
    public function handleErrors($code, $error, $errFile, $errLine) {
        self::$_errors[] = $code . $errFile . ' ' . $errLine . ' ' . $error;
    }
    
    private function _skipRequest($request) {
        return array(
            'data'    => false,
            'request' => $request,
            'success' => false,
            'error'   => self::ERROR_MESSAGE_SKIPPED_REQUEST,
            'code'    => self::METHOD_ERROR_CODE_SKIPPED,
            'detail'  => null,
            'buffer'  => null
        );
    }
    
    private function _handleRequest($request) {
        $_service = $request['service'];
        $_method  = $request['method'];
        $_params  = isset($request['params']) ? $request['params'] : array();
        $_success = true;
        $_res     = null;
        $_error   = null;
        $_detail = null;
        $_code    = 0;
        $_buffer = null;
        
        // check class existance
        if($_success and !class_exists($_service)) {
            $_success = false;
            $_error = array(
                'code'    => self::ERROR_CODE_INVALID_SERVICE,
                'message' => self::ERROR_MESSAGE_INVALID_SERVICE,
                'data'    => $request
            );
            $_success = false;
        }

        // instanciate service if not yet done
        $_success and !isset($this->_services[$_service]) and $this->_services[$_service] = $this->_factoryServiceXhr->getInstanceByName($_service); // xhr service
        $_success and $this->_services[$_service]->factoryService = $this->_factoryService; // autolink non-xhr service
        
        // check method existance
        if($_success and !method_exists($this->_services[$_service], $_method)) {
            $_success = false;
            $_error   = array(
                'code'    => self::ERROR_CODE_INVALID_METHOD,
                'message' => self::ERROR_MESSAGE_INVALID_METHOD,
                'data'    => $request
            );
            $_success = false;
        }
        // call method
        if($_success) {
            $_access = $this->_services[$_service]->isMethodAccessible($_method);
            if($_access) {
                try {
                    ob_start();
                    set_error_handler(array($this, 'handleErrors'), E_ALL | E_STRICT);
                    
                    $_res = call_user_func(array($this->_services[$_service], $_method), $_params);
                    
                    $_buffer = ob_get_flush();
                    restore_error_handler();
                } catch (Berthe_ErrorHandler $e) {
                    $_res     = false;
                    $_success = false;
                    $e->reset();
                    $_errs = array();
                    while(($error = $e->fetch()) !== false) {
                        $_errs[] = $error->getMessage();
                    }
                    $_error = implode("<br />\n", $_errs);
                    $_detail  =  $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' ' . $e->getTraceAsString();
                    $_code    = self::METHOD_ERROR_LOGIC_EXCEPTION;
                } catch (Exception $e) {
                    $_res     = false;
                    $_success = false;
                    $_error   = $e->getMessage();
                    $_detail  =  $e->getFile() . ' ' . $e->getLine() . ' ' . $e->getTraceAsString();
                    $_code    = self::METHOD_ERROR_CODE_EXCEPTION;
                } 
            } else {
                $_res     = false;
                $_success = false;
                $_error   = self::METHOD_ERROR_MESSAGE_UNAUTHORIZED;
                $_code    = self::METHOD_ERROR_CODE_UNAUTHORIZED;
            }
        }
        
        !$_success and $this->_success = false;
        
        !$_success and $this->_error['code']    &= self::ERROR_CODE_WRONG_REQUEST;
        !$_success and $this->_error['message'] .= ' ' . self::ERROR_MESSAGE_WRONG_REQUEST;
        return array(
            'data'    => $_res,
            'request' => $request,
            'success' => $_success,
            'error'   => $_error,
            'code'    => $_code,
            'detail'  => $_detail,
            'buffer'  => $_buffer
        );
    }
    
    private function _makeResult() {
        $_errors = self::$_errors;
        self::$_errors = array();
        return array(
            'success'  => $this->_success,
            'error'    => $this->_error,
            'result'   => $this->_result,
            'requests' => $this->_requests,
            'errors'   => $_errors,
            'translations_ok' => Translation_Manager::getInstance()->dumpCacheLevel1(),
            'translations_ko' => Translation_Manager::getInstance()->getNotFounds(),
            'analytics'       => Evaneos_Analytics::getInstance()->getJsonable()
        );
    }
}