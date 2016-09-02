<?php
session_start();
/**
 * @name UtilController
 * @author yangmingfu
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class UtilController extends Yaf_Controller_Abstract {
	public function captchaAction() {
        $objVc = new Util_Validate();
        $objVc->doimg();
        $_SESSION['authnum_session'] = $objVc->getCode();
	}
}
