<?php
Yaf_Loader::import(dirname(__FILE__) . "/Base.php");
class ProclamationAction extends BaseAction {
    public function getList(Yaf_Request_Http $request){
        $objProclamationBiz = new Biz_Proclamation();
        $objUserBiz = new Biz_User();
        $query = $request->getQuery();
        $list = $objProclamationBiz->getList($query);
        foreach ($list as $k=>$v) {
            $return['id'] = $v['id'];
            $return['title'] = $v['title'];
            $return['content'] = $v['content'];
            $return['status'] = $v['status'];
            $return['created'] = date('Y-m-d H:i:s', $v['created']);
            $return['operator'] = $v['operator'];
            $operators[$v['operator']] = $v['operator'];
            $result[$v[['id']]] = $return;
        }
        $operators = $objUserBiz->getList(join(',', $operators));
        $this->getView()->assign('result', $result);
        $this->getView()->assign('operators', $operators);
        return TRUE;
    }
    public function get(Yaf_Request_Http $request) {
        $id = $request->getPost('id');
        $objProclamationBiz = new Biz_Proclamation();
        $list = $objProclamationBiz->getList(array('id'=>$id));
        foreach ($list as $k=>$v) {
            $return['id'] = $v['id'];
            $return['title'] = $v['title'];
            $return['content'] = $v['content'];
        }
        $this->ajaxResult['data']['list'] = $return;
        $this->ajaxResult['data']['type'] = 'detail';
        exit(json_encode($this->ajaxResult));
    }
    public function create(Yaf_Request_Http $request) {
        $title = $request->getPost('title', null);
        $content = $request->getPost('content', null);
        if (empty($title)) {
            exit($this->setError(' 标题不能为空'));
        }
        if (empty($content)) {
            exit($this->setError(' 内容不能为空'));
        }
        $data['title'] = $title;
        $data['content'] = $content;
        $data['status'] = 1;
        $objProclamationBiz = new Biz_Proclamation(); 
        $res = $objProclamationBiz->create($data);
        if (false === $res) {
            exit($this->setError(' 该公告已经存在')); 
        }
        if ($res <= 0) {
            exit($this->setError('添加失败')); 
        }
        exit(json_encode($this->ajaxResult));
    }

    public function modify(Yaf_Request_Http $request) {
        $id = $request->getPost('proclamationid', null);
        $title = $request->getPost('title', null);
        $content = $request->getPost('content', null);
        $status = $request->getPost('status', null);
        $objProclamationBiz = new Biz_Proclamation(); 
        if (empty($id)) {
            exit($this->setError(' id不能为空'));
        }
        if (!empty($status) && $status != null) {
            $data['id'] = $id;
            $data['status'] = $status;
            $res = $objProclamationBiz->modify($data);
            if ($res <= 0) {
                exit($this->setError(' 修改失败')); 
            }
            exit(json_encode($this->ajaxResult));
        }

        if (empty($title)) {
            exit($this->setError(' 标题不能为空'));
        }
        $data['id'] = $id;
        $data['title'] = $title;
        $data['content'] = $content;
        $res = $objProclamationBiz->modify($data);
        if ($res <= 0) {
            exit($this->setError(' 修改失败')); 
        }
        exit(json_encode($this->ajaxResult));
    }

    public function delete(Yaf_Request_Http $request) {
        $id = $request->getPost('proclamationid', null);
        if (empty($id)) {
            exit($this->setError('id不能为空'));
        }
        $objProclamationBiz = new Biz_Proclamation();
        $res = $objProclamationBiz->delete($id);
        if ($res <= 0) {
            exit($this->setError(' 删除失败')); 
        }
        exit(json_encode($this->ajaxResult));
    }
}
