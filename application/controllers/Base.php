<?php
/**
 * @name BaseController
 * @author yangmingfu
 * @desc 公共控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class BaseController extends Yaf_Controller_Abstract {
    public $ajaxResult = array(
        'status' => 200,
        'data' => array(),
        'msg' => '',
    );
    public $_view_path;

    public function init() {
        $this->_view_path = dirname(__FILE__) . '/../views/';
        $this->getView()->assign('viewPath', $this->_view_path);
        if ($this->getRequest()->isXmlHttpRequest()) {
            Yaf_Dispatcher::getInstance()->disableView();
            return;
        } 
        if (strtolower($this->getRequest()->controller) == 'login') {
            return;
        }
        //登录检测
        $objLoginBiz = new Biz_Login();
        if (!$objLoginBiz->isLogin()) {
            $this->redirect("/login/login");
        } 
        //设置每个页面需要的value
        $config = Yaf_Registry::get('config');
        //页面TITLE
        $this->getView()->assign('pageTitle', $config['application']['title']);
        //页脚
        $this->getView()->assign('footerRight', "Copyright © 2015-" . date('Y'));
        //用户信息
        $currentUser = Util_Tools::getCurrentUser();
        $this->getView()->assign('currentUser', $currentUser);
        //用户显示菜单
        $objMenuBiz = new Biz_Menu();
        $currentMenu = $objMenuBiz->getCurrentMenu($currentUser['roleid']);
        $this->getView()->assign('currentMenu', $currentMenu);
        //菜单选中状态
        $c = strtolower($this->getRequest()->controller);
        $a = strtolower($this->getRequest()->action);
        $this->getView()->assign('c', $c);
        $this->getView()->assign('a', $a);
    }

    public function setError($msg) {
        $this->ajaxResult['status'] = -1;
        $this->ajaxResult['msg'] = $msg;
        return json_encode($this->ajaxResult);
    }
}
