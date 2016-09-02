<?php
Yaf_Loader::import(dirname(__FILE__) . "/Base.php");
class DutiesAction extends BaseAction {
    public function getList(Yaf_Request_Http $request){
        $objDutiesBiz = new Biz_Duties();
        $objUserBiz = new Biz_User();

        $list = $objDutiesBiz->getList();
        foreach ($list as $k=>$v) {
            $return['id'] = $v['id'];
            $return['name'] = $v['name'];
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
        if (is_null($name) || empty($name)) {
            $this->ajaxResult['status'] = -1;
            $this->ajaxResult['msg'] = ' 职位名称不能为空 ';
            exit(json_encode($this->ajaxResult));
        }
        $objDutiesBiz = new Biz_Duties();
        $id = $objDutiesBiz->create($name);
        if ($id === false) {
            $this->ajaxResult['status'] = -1;
            $this->ajaxResult['msg'] = ' 职位名称重复 ';
        }
        exit(json_encode($this->ajaxResult));
    }
    public function modify(Yaf_Request_Http $request) {
        $id = $request->getPost('dutiesid', null);
        $name = $request->getPost('name', null);
        if (is_null($id) || empty($id)) {
            $this->ajaxResult['status'] = -1;
            $this->ajaxResult['msg'] = ' 职位ID不能为空 ';
            exit(json_encode($this->ajaxResult));
        }
        if (is_null($name) || empty($name)) {
            $this->ajaxResult['status'] = -1;
            $this->ajaxResult['msg'] = ' 职位名称不能为空 ';
            exit(json_encode($this->ajaxResult));
        }
        $objDutiesBiz = new Biz_Duties();
        $row = $objDutiesBiz->modify($id, $name, $ids);
        if ($row === false) {
            $this->ajaxResult['status'] = -1;
            $this->ajaxResult['msg'] = ' 职位名称重复 ';
        }
        exit(json_encode($this->ajaxResult));

    }
    public function delete(Yaf_Request_Http $request) {

    }
}
