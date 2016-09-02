<?php
/**
 * @name ErrorController
 * @desc 错误控制器, 在发生未捕获的异常时刻被调用
 * @see http://www.php.net/manual/en/yaf-dispatcher.catchexception.php
 * @author yangmingfu
 */
class ErrorController extends Yaf_Controller_Abstract {
    /**
     * 此时可通过$request->getException()获取到发生的异常
     */
    public function errorAction() {
        $exception = $this->getRequest()->getException();
        try {
            throw $exception;
        } catch (Yaf_Exception_LoadFailed $e) {
            //加载失败
        } catch (Yaf_Exception $e) {
            //其他错误
        }
        $this->getView()->assign('exception', $exception);
        return TRUE;
    }
}
