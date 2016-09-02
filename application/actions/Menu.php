<?php
Yaf_Loader::import(dirname(__FILE__) . "/Base.php");
class MenuAction extends BaseAction {
    public function getList(Yaf_Request_Http $request){
        $objMenuBiz = new Biz_Menu();
        $objUserBiz = new Biz_User();

        $list = $objMenuBiz->getList();
        foreach ($list as $k=>$v) {
            $return['name'] = $v['name'];
            $return['created'] = date('Y-m-d H:i:s', $v['created']);
            $return['operator'] = $v['operator'];
            $return['parentid'] = $v['parentid'];
            $return['accessid'] = $v['accessid'];
            $operators[$v['operator']] = $v['operator'];
            $result[] = $return;
        }
        $operators = $objUserBiz->getList(join(',', $operators));
        $this->getView()->assign('result', $result);
        $this->getView()->assign('operators', $operators);
        return TRUE;
    }
    public function create(Yaf_Request_Http $request){

    }
    public function modify(Yaf_Request_Http $request) {

    }
    public function delete(Yaf_Request_Http $request) {

    }
}
