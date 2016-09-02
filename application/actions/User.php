<?php
Yaf_Loader::import(dirname(__FILE__) . "/Base.php");
class UserAction extends BaseAction {
    public function getList(Yaf_Request_Http $request){
        $objUserBiz = new Biz_User();
        $objRoleBiz = new Biz_Role();
        $objDutiesBiz = new Biz_Duties();
        $query = $request->getQuery();
        $list = $objUserBiz->getList($query);
        foreach ($list as $k=>$v) {
            $return['id'] = $v['id'];
            $return['name'] = $v['name'];
            $return['gender'] = $v['gender'];
            $return['duties'] = $v['duties'];
            $return['mobile'] = $v['mobile'];
            $return['email'] = $v['email'];
            $return['status'] = $v['status'];
            $return['created'] = date('Y-m-d H:i:s', $v['created']);
            $return['operator'] = $v['operator'];
            $return['roleid'] = $v['roleid'];
            $roleids[$v['roleid']] = $v['roleid'];
            $operators[$v['operator']] = $v['operator'];
            $result[$v['id']] = $return;
        }
        $roles = $objRoleBiz->getList();
        $operators = $objUserBiz->getList(join(',', $operators));
        $duties = $objDutiesBiz->getList();
        $this->getView()->assign('result', $result);
        $this->getView()->assign('roles', $roles);
        $this->getView()->assign('operators', $operators);
        $this->getView()->assign('duties', $duties);
        return TRUE;
    }
    public function create(Yaf_Request_Http $request) {
        $name = $request->getPost('name', null);
        $mobile = $request->getPost('mobile', null);
        $email = $request->getPost('email', null);
        $roleid = $request->getPost('roleid', -1);
        $gender = $request->getPost('gender', '男');
        $duties = $request->getPost('duties', -1);
        $status = $request->getPost('status', 1);
        $pass = substr(md5(mt_rand(10, 9001230)), mt_rand(0, 23), 8);
        if (empty($name)) {
            exit($this->setError(' 姓名不能为空'));
        }
        if (empty($mobile) || !Util_Tools::isMobile($mobile)) {
            exit($this->setError(' 手机号不能为空或格式不正确'));
        }
        if (empty($email) || !Util_Tools::isMail($email)) {
            exit($this->setError(' 邮箱不能为空或格式不正确'));
        }
        list($passwd, $salt) = Util_Salt::genPasswd($pass);
        $this->ajaxResult['data'] = $pass;
        $data['name'] = $name;
        $data['mobile'] = $mobile;
        $data['email'] = $email;
        $data['status'] = $status;
        if (!empty($roleid) && $roleid != -1) $data['roleid'] = $roleid;
        if (!empty($duties) && $duties!= -1) $data['duties'] = $duties;
        $data['gender'] = $gender;
        $data['passwd'] = $passwd;
        $data['salt'] = $salt;
        $objUserBiz = new Biz_User(); 
        $res = $objUserBiz->create($data);
        if (false === $res) {
            exit($this->setError('该手机号已经存在')); 
        }
        if ($res <= 0) {
            exit($this->setError('添加失败')); 
        }
        exit(json_encode($this->ajaxResult));
    }
    public function modify(Yaf_Request_Http $request) {
        $id = $request->getPost('id', null);
        $name = $request->getPost('name', null);
        $mobile = $request->getPost('mobile', null);
        $email = $request->getPost('email', null);
        $roleid = $request->getPost('roleid', -1);
        $gender = $request->getPost('gender', '男');
        $duties = $request->getPost('duties', -1);
        $status = $request->getPost('status', 1);
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
        if (!empty($roleid) && $roleid != -1) $data['roleid'] = $roleid;
        if (!empty($duties) && $duties!= -1) $data['duties'] = $duties;
        $data['status'] = $status;
        $data['gender'] = $gender;
        $objUserBiz = new Biz_User(); 
        $res = $objUserBiz->modify($data);
        if ($res <= 0) {
            exit($this->setError('修改失败')); 
        }
        exit(json_encode($this->ajaxResult));
    }

    public function delete(Yaf_Request_Http $request) {
        $id = $request->getPost('id', null);
        if (empty($id)) {
            exit($this->setError('id不能为空'));
        }
        $objUserBiz = new Biz_User();
        $res = $objUserBiz->delete($id);
        if (false === $res){
            exit($this->setError(' 自己不能删除自己')); 
        }
        if ($res <= 0) {
            exit($this->setError(' 删除失败')); 
        }
        exit(json_encode($this->ajaxResult));

    }
}
