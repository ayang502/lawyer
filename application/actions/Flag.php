<?php
Yaf_Loader::import(dirname(__FILE__) . "/Base.php");
class FlagAction extends BaseAction {
    public function getList(Yaf_Request_Http $request){
        $objFlagBiz = new Biz_Flag();
        $objUserBiz = new Biz_User();
        $list = $objFlagBiz->getList();
        foreach ($list as $k=>$v) {
            $return['id'] = $v['id'];
            $return['name'] = $v['name'];
            $return['status'] = $v['status'];
            $return['files'] = json_decode($v['files'], true);
            $return['created'] = date('Y-m-d H:i:s', $v['created']);
            $return['operator'] = $v['operator'];
            $operators[$v['operator']] = $v['operator'];
            $result[] = $return;
        }
        $operators = $objUserBiz->getList(join(',', $operators));
        $this->getView()->assign('result', $result);
        $this->getView()->assign('operators', $operators);
        return TRUE;
    }
    public function create(Yaf_Request_Http $request){
        $name = $request->getPost('name', null);
        $files = $request->getPost('files', null);
        $need = $request->getPost('need', null);
        if (is_null($name) || empty($name)) {
            $this->ajaxResult['status'] = -1;
            $this->ajaxResult['msg'] = ' 阶段名称不能为空 ';
            exit(json_encode($this->ajaxResult));
        }
        $objFlagBiz = new Biz_Flag();
        $id = $objFlagBiz->create($name, $files, $need);
        if ($id === false) {
            $this->ajaxResult['status'] = -1;
            $this->ajaxResult['msg'] = ' 阶段名称重复 ';
        }
        exit(json_encode($this->ajaxResult));
    }
    public function modify(Yaf_Request_Http $request) {
        $id = $request->getPost('Flagid', null);
        $name = $request->getPost('name', null);
        $files = $request->getPost('files', null);
        $need = $request->getPost('need', null);
        $status = $request->getPost('status', null);
        $objFlagBiz = new Biz_Flag();
        if (empty($id)) {
            exit($this->setError(' ID不能为空')); 
        }
        if (!empty($status) && $status != null) {
            $res = $objFlagBiz->modify($id, '', array(), array(), $status);
            if ($res <= 0) {
                exit($this->setError(' 修改失败')); 
            }
            exit(json_encode($this->ajaxResult));
        }

        if (is_null($name) || empty($name)) {
            $this->ajaxResult['status'] = -1;
            $this->ajaxResult['msg'] = ' 阶段名称不能为空 ';
            exit(json_encode($this->ajaxResult));
        }
        $row = $objFlagBiz->modify($id, $name, $files, $need);
        if ($row === false) {
            $this->ajaxResult['status'] = -1;
            $this->ajaxResult['msg'] = ' 阶段名称重复 ';
        }
        exit(json_encode($this->ajaxResult));
    }
    public function delete(Yaf_Request_Http $request) {
        $id = $request->getPost('id', null);
        if (empty($id)) {
            exit($this->setError('id不能为空'));
        }
        $objFlagBiz = new Biz_Flag();
        $res = $objFlagBiz->delete($id);
        if ($res <= 0) {
            exit($this->setError(' 删除失败')); 
        }
        exit(json_encode($this->ajaxResult));
    }
}
