<?php
class Biz_User {
    public function __construct() {
        $this->objUserModel = new UserModel();
    }
    public function getList($query = array(), $in = false) {
        if ($in === true) {
            $this->objUserModel->where($query);
            unset($query);
        }
        if (isset($query['search']) && !empty($query['search'])) {
            $datas['mobile'] = $query['search'];
            $datas['name'] = $query['search'];
            $datas['email'] = $query['search'];
            $this->objUserModel->whereLike($datas, 'or');
        } else if(is_array($query) && !empty($query)) {
            $this->objUserModel->whereAnd($query);
        }
        if (!is_array($query) && !empty($query)) {
            $list = $this->objUserModel->fList($query);
        } else {
            $list = $this->objUserModel->fList();
        }
        return Util_Tools::changArrKey($list, 'id');
    }
    public function getUser($key, $value) {
        if (empty($query)) {
            return array();
        }
        $method = "ff" . ucfirst($key);
        return $this->objUserModel->$method($value);
    }
    public function create($data = array()) {
        if (empty($data)){
            return 0;
        }
        $list = $this->objUserModel->ffMobile($data['mobile']);
        if (!empty($list)) {
            return false;
        }
        $data['operator'] = Util_Tools::getCurrentUser()['id'];
        $data['created'] = time();
        $id = $this->objUserModel->insert($data);
        return $id;
    }
    public function modify($data = array()) {
        $list = $this->objUserModel->ffId($data['id']);
        if (empty($list)) {
            return false;
        }
        if (!empty($list) && $list['id'] != $data['id']) {
            return false;
        }
        $list = $this->objUserModel->ffMobile($data['mobile']);
        if (!empty($list) && $list['id'] != $data['id']) {
            return false;
        }
        $list = $this->objUserModel->ffEmail($data['email']);
        if (!empty($list) && $list['id'] != $data['id']) {
            return false;
        }
        $data['operator'] = Util_Tools::getCurrentUser()['id'];
        $rows = $this->objUserModel->update($data);
        return $rows;
    }
    public function delete($id) {
        if ($id == Util_Tools::getCurrentUser()['id']) {
            return false;
        }
        $data['operator'] = Util_Tools::getCurrentUser()['id'];
        $data['id'] = $id;
        $data['status'] = '-1';
        $rows = $this->objUserModel->update($data);
        return $rows;
    }
}
