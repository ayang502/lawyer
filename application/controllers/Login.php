<?php
/**
 * @name LoginController
 * @author yangmingfu
 * @desc 登录控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class LoginController extends BaseController {
	public function LoginAction() {
        return TRUE;
	}

    public function LogoutAction() {
        setcookie("ua", "", time());
        $this->redirect('/login/login');
    }
    public function LoginAjaxAction() {
        session_start();
        $mobile = $this->getRequest()->getPost("mobile", null);
		$passwd = $this->getRequest()->getPost("passwd", null);
        $captcha = $this->getRequest()->getPost("captcha", null);
        if (!Util_Tools::isMobile($mobile)) {
            $this->ajaxResult['status'] = -1;
            $this->ajaxResult['msg'] = '手机号格式不正确';
            exit(json_encode($this->ajaxResult));
        }
        if (empty($passwd)) {
            $this->ajaxResult['status'] = -1;
            $this->ajaxResult['msg'] = '密码不能为空';
            exit(json_encode($this->ajaxResult));
        }
        if (empty($captcha)) {
            $this->ajaxResult['status'] = -1;
            $this->ajaxResult['msg'] = '验证码不能为空';
            exit(json_encode($this->ajaxResult));
        }
        if (strtolower($captcha) != strtolower($_SESSION['authnum_session'])) {
            $this->ajaxResult['status'] = -1;
            $this->ajaxResult['msg'] = '验证码不正确';
            exit(json_encode($this->ajaxResult));
        }
        $objLoginBiz = new Biz_Login();
        $res = $objLoginBiz->login(array($mobile, $passwd));
        if (!$res) {
            $this->ajaxResult['status'] = -1;
            $this->ajaxResult['msg'] = '手机号或密码错误';
        }
        exit(json_encode($this->ajaxResult));
    }
}
