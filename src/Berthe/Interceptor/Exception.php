<?php
class Berthe_Interceptor_Exception extends Berthe_AbstractInterceptor {
    public function intercept($method, $args) {
        try {
            return $this->invoke($method, $args);
        }
        catch(LogicException $e) {
            throw $e;
        }
        catch(RuntimeException $e) {
            throw $e;
        }
        catch(Exception $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}