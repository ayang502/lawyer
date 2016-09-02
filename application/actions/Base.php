<?php
abstract class BaseAction extends Yaf_Action_Abstract {
    public $ajaxResult = array(
        'status' => 200,
        'data' => array(),
        'msg' => '',
    );
    public function execute () {
        $params = $this->getRequest()->getParams();
        $request = $this->getRequest();
        if (!isset($params['act'])) {
            $this->getList($request);
            return TRUE;
        }
        switch ($params['act']) {
            case 'list': $this->getList($request); break;
            case 'create': $this->create($request); break;
            case 'modify': $this->modify($request); break;
            case 'delete': $this->delete($request); break;
            case 'get': $this->get($request); break;
            default:$this->getList($request); break;
        }
        return TRUE;
    }
    public function setError($msg) {
        $this->ajaxResult['status'] = -1;
        $this->ajaxResult['msg'] = $msg;
        return json_encode($this->ajaxResult);
    }
    abstract public function getList(Yaf_Request_Http $request);
    abstract public function create(Yaf_Request_Http $request);
    abstract public function modify(Yaf_Request_Http $request);
    abstract public function delete(Yaf_Request_Http $request);
}
