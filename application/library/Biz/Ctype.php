<?php
class Biz_Ctype {
    public function __construct() {
        $this->objCtypeModel = new CtypeModel();
    }
    public function getList($query = array()) {
        if (isset($query['search']) && !empty($query['search'])) {
            $datas['name'] = $query['search'];
            $this->objCtypeModel->whereLike($datas, 'and');
        } else {
            if (isset($query['a-level']) && !empty($query['a-level'])) {
                $query['level'] = $query['a-level'];
                unset($query['a-level']);
            }
            $this->objCtypeModel->whereAnd($query);
        } 
        $list = $this->objCtypeModel->fList();
        return Util_Tools::changArrKey($list, 'id');
    }

    public function getTree() {
        $list = $this->objCtypeModel->fList();
        $list = Util_Tools::changArrKey($list, 'id');
    }

    public function getCtype($key, $value) {
        if (empty($query)) {
            return array();
        }
        $method = "ff" . ucfirst($key);
        return $this->objCtypeModel->$method($value);
    }

    public function create($data = array()) {
        if (empty($data)){
            return 0;
        }
        $list = $this->objCtypeModel->ffName($data['name']);
        if (!empty($list)) {
            return false;
        }
        $data['operator'] = Util_Tools::getCurrentUser()['id'];
        $data['created'] = time();
        $id = $this->objCtypeModel->insert($data);
        return $id;
    }

    public function modify($data = array()) {
        $list = $this->objCtypeModel->ffId($data['id']);
        if (empty($list)) {
            return false;
        }
        if (!empty($list) && $list['id'] != $data['id']) {
            return false;
        }
        $list = $this->objCtypeModel->ffName($data['name']);
        if (!empty($list) && $list['id'] != $data['id']) {
            return false;
        }
        $data['operator'] = Util_Tools::getCurrentUser()['id'];
        $rows = $this->objCtypeModel->update($data);
        return $rows;
    }
}
