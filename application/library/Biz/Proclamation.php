<?php
class Biz_Proclamation {
    public function __construct() {
        $this->objProclamationModel = new ProclamationModel();
    }
    public function getList($query = array()) {
        if (isset($query['search']) && !empty($query['search'])) {
            $datas['mobile'] = $query['search'];
            $datas['name'] = $query['search'];
            $datas['email'] = $query['search'];
            $this->objProclamationModel->whereLike($datas, 'or');
        } else {
            $this->objProclamationModel->whereAnd($query);
        } 
        $list = $this->objProclamationModel->fList();
        return Util_Tools::changArrKey($list, 'id');
    }
    public function create($data = array()) {
        if (empty($data)){
            return 0;
        }
        $list = $this->objProclamationModel->ffTitle($data['title']);
        if (!empty($list)) {
            return false;
        }
        $data['operator'] = Util_Tools::getCurrentUser()['id'];
        $data['created'] = time();
        $id = $this->objProclamationModel->insert($data);
        return $id;
    }
    public function modify($data = array()) {
        $list = $this->objProclamationModel->ffId($data['id']);
        if (empty($list)) {
            return false;
        }
        if (!empty($list) && $list['id'] != $data['id']) {
            return false;
        }
        $data['operator'] = Util_Tools::getCurrentUser()['id'];
        $rows = $this->objProclamationModel->update($data);
        return $rows;
    }
    public function delete($id) {
        $data['operator'] = Util_Tools::getCurrentUser()['id'];
        $data['id'] = $id;
        $data['status'] = '-1';
        $rows = $this->objProclamationModel->update($data);
        return $rows;
    }
}
