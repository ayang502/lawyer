<?php
Yaf_Loader::import(dirname(__FILE__) . "/Base.php");
class RoleAction extends BaseAction {
    public function getList(Yaf_Request_Http $request){
        $objRoleBiz = new Biz_Role();
        $objUserBiz = new Biz_User();
        $objMenuBiz = new Biz_Menu();

        $list = $objRoleBiz->getList();
        foreach ($list as $k=>$v) {
            $return['id'] = $v['id'];
            $return['name'] = $v['name'];
            $return['created'] = date('Y-m-d H:i:s', $v['created']);
            $return['operator'] = $v['operator'];
            $return['resourceid'] = $v['resourceid'];
            $return['perm'] = $objMenuBiz->getMenuById($v['resourceid']);
            $operators[$v['operator']] = $v['operator'];
            $result[] = $return;
        }
        $operators = $objUserBiz->getList(join(',', $operators));
        $this->getView()->assign('result', $result);
        $this->getView()->assign('menuList', $objMenuBiz->changeToArray());
        $this->getView()->assign('operators', $operators);
        return TRUE;
    }
    public function create(Yaf_Request_Http $request){
        $name = $request->getPost('name', null);
        $ids = $request->getPost('ids', null);
        if (is_null($name) || empty($name)) {
            $this->ajaxResult['status'] = -1;
            $this->ajaxResult['msg'] = ' 角色名称不能为空 ';
            exit(json_encode($this->ajaxResult));
        }
        $objRoleBiz = new Biz_Role();
        $id = $objRoleBiz->create($name, $ids);
        if ($id === false) {
            $this->ajaxResult['status'] = -1;
            $this->ajaxResult['msg'] = ' 角色名称重复 ';
        }
        exit(json_encode($this->ajaxResult));
    }
    public function modify(Yaf_Request_Http $request) {
        $id = $request->getPost('roleid', null);
        $name = $request->getPost('name', null);
        $ids = $request->getPost('ids', null);
        if ($id == 1) {
            $this->ajaxResult['status'] = -1;
            $this->ajaxResult['msg'] = ' 系统管理员不能更改 ';
            exit(json_encode($this->ajaxResult));
        }
        if (is_null($id) || empty($id)) {
            $this->ajaxResult['status'] = -1;
            $this->ajaxResult['msg'] = ' 角色ID不能为空 ';
            exit(json_encode($this->ajaxResult));
        }
        if (is_null($name) || empty($name)) {
            $this->ajaxResult['status'] = -1;
            $this->ajaxResult['msg'] = ' 角色名称不能为空 ';
            exit(json_encode($this->ajaxResult));
        }
        $objRoleBiz = new Biz_Role();
        $row = $objRoleBiz->modify($id, $name, $ids);
        if ($row === false) {
            $this->ajaxResult['status'] = -1;
            $this->ajaxResult['msg'] = ' 角色名称重复 ';
        }
        exit(json_encode($this->ajaxResult));

    }
    public function delete(Yaf_Request_Http $request) {

    }
}
