<?php
/**
 * @name SettingController
 * @author yangmingfu
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class SettingController extends BaseController {
	public function profileAction() {
        $objUserBiz = new Biz_User();
        $objRoleBiz = new Biz_Role();
        $objDutiesBiz = new Biz_Duties();
        $id = Util_Tools::getCurrentUser()['id'];
        $return = $objUserBiz->getList($id)[$id];
        unset($return['created'], $return['operator'], $result['status'], $result['updated'], $result['passwd'], $result['salt']);
        $roles = $objRoleBiz->getList($return['roleid']);
        $duties = $objDutiesBiz->getList($return['duties']);
        $this->getView()->assign('result', $return);
        $this->getView()->assign('roles', $roles);
        $this->getView()->assign('duties', $duties);
        return TRUE;
	}
    public function repassAction() {
        return TRUE;
    }
    public function reAction() {
        $request = $this->getRequest();
        $oldPass = $request->getPost('oldpass', null);
        $newPass = $request->getPost('newpass', null);
        $renewPass = $request->getPost('renewpass', null);
        if ($oldPass != $rePass) {
            exit($this->setError(' 旧密码与新密码不能相同'));
        }
        if ($newPass != $renewPass) {
            exit($this->setError(' 两次密码输入不一致'));
        }
        if (!Util_Tools::isPass($newPass)) {
            exit($this->setError(''));
        }
        $id = Util_Tools::getCurrentUser()['id'];
        $objUserBiz = new Biz_User();
        $return = $objUserBiz->getList($id)[$id];
        list($pass, $salt) = Util_Salt::genPasswd($oldPass, $return['salt']);
        if ($pass != $return['password']) {
            exit($this->setError(' 输入的密码错误'));
        }
        $data['id'] = $id;
        $data['password'] = $pass;
        $res = $objUserBiz->modify($data);
        exit(json_encode($this->ajaxResult));
    }
    public function modifyAction() {
        $request = $this->getRequest();
        $id = Util_Tools::getCurrentUser()['id'];
        $name = $request->getPost('name', null);
        $mobile = $request->getPost('mobile', null);
        $email = $request->getPost('email', null);
        $gender = $request->getPost('gender', '男');
        if (empty($id)) {
            exit($this->setError(' id不能为空'));
        }
        if (empty($name)) {
            exit($this->setError(' 姓名不能为空'));
        }
        if (empty($mobile) || !Util_Tools::isMobile($mobile)) {
            exit($this->setError(' 手机号不能为空或格式不正确'));
        }
        if (empty($email) || !Util_Tools::isMail($email)) {
            exit($this->setError(' 邮箱不能为空或格式不正确'));
        }
        $data['id'] = $id;
        $data['name'] = $name;
        $data['mobile'] = $mobile;
        $data['email'] = $email;
        $data['gender'] = $gender;
        $objUserBiz = new Biz_User(); 
        $res = $objUserBiz->modify($data);
        exit(json_encode($this->ajaxResult));
    }
}
