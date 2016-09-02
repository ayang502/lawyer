<?php
Yaf_Loader::import(dirname(__FILE__) . "/Base.php");
class CtypeAction extends BaseAction {
    public function getList(Yaf_Request_Http $request){
        $objCtypeBiz = new Biz_Ctype();
        $objUserBiz = new Biz_User();
        $query = $request->getPost() + $request->getQuery();
        if (isset($query['level'])){
            $query['level'] = $query['level'] - 1;
        }
        $list = $objCtypeBiz->getList($query);
        foreach ($list as $id=>$v) {
            $return['id'] = $v['id'];
            $return['name'] = $v['name'];
            $return['level'] = $v['level'];
            $return['level_zh'] = Util_Tools::num2zh($v['level']);
            $return['parentid'] = $v['parentid'];
            $return['created'] = date('Y-m-d H:i:s', $v['created']);
            $return['operator'] = $v['operator'];
            $operators[$v['operator']] = $v['operator'];
            $result[$id] = $return;
        }
        $operators = $objUserBiz->getList(join(',', $operators));
        $this->getView()->assign('result', $result);
        $this->getView()->assign('operators', $operators);
        if ($this->getRequest()->isXmlHttpRequest()) {
            Yaf_Dispatcher::getInstance()->disableView();
            if (!empty($list)) {
                $this->ajaxResult['data']['list'] = $list;
                $this->ajaxResult['data']['type'] = 'level';
                exit(json_encode($this->ajaxResult));
            } else {
                exit($this->setError('没有该层级分类')); 
            }
            exit();
        }
        return TRUE;
    }
    public function create(Yaf_Request_Http $request) {
        $name = $request->getPost('name', null);
        $level = $request->getPost('level', 1);
        $parentid = $request->getPost('parentid', 0);
        if (empty($name)) {
            exit($this->setError(' 名称不能为空'));
        }
        $data['name'] = $name;
        $data['level'] = $level;
        $data['parentid'] = $parentid;
        $objCtypeBiz = new Biz_Ctype(); 
        $res = $objCtypeBiz->create($data);
        if (false === $res) {
            exit($this->setError('该类型名称已经存在')); 
        }
        if ($res <= 0) {
            exit($this->setError('添加失败')); 
        }
        exit(json_encode($this->ajaxResult));
    }
    public function modify(Yaf_Request_Http $request) {
        $id = $request->getPost('id', null);
        $name = $request->getPost('name', null);
        $level = $request->getPost('level', null);
        $parentid = $request->getPost('parentid', null);
        if (empty($id)) {
            exit($this->setError(' id不能为空'));
        }
        if (empty($name)) {
            exit($this->setError(' 姓名不能为空'));
        }
        $data['id'] = $id;
        $data['name'] = $name;
        $objCtypeBiz = new Biz_Ctype(); 
        $res = $objCtypeBiz->modify($data);
        if ($res <= 0) {
            exit($this->setError('修改失败')); 
        }
        exit(json_encode($this->ajaxResult));
    }

    public function delete(Yaf_Request_Http $request) {
    }
}
