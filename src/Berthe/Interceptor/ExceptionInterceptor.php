<?php
namespace Berthe\Interceptor;


class ExceptionInterceptor extends AbstractInterceptor implements Interceptor {
    protected function intercept($method, $args) {
        try {
            return $this->invoke($method, $args);
        }
        catch(\LogicException $e) {
            throw $e;
        }
        catch(\RuntimeException $e) {
            throw $e;
        }
        catch(\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}