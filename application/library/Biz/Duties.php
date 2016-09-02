<?php
class Biz_Duties {
    public function __construct() {
        $this->objDutiesModel = new DutiesModel();
    }
    public function getList($query = array()) {
        $list = $this->objDutiesModel->fList($query);
        return Util_Tools::changArrKey($list, 'id');
    }
    public function create($name) {
        $list = $this->objDutiesModel->ffName($name);
        if (!empty($list)) {
            return false;
        }
        $data['name'] = $name;
        $data['operator'] = Util_Tools::getCurrentUser()['id'];
        $data['created'] = time();
        $dutiesid = $this->objDutiesModel->insert($data);
        return $dutiesid;
    }
    public function modify($id, $name) {
        $list = $this->objDutiesModel->ffId($id);
        if (empty($list)) {
            return false;
        }
        if (!empty($list) && $list['id'] != $id) {
            return false;
        }
        $data['id'] = $id;
        $data['name'] = $name;
        $data['operator'] = Util_Tools::getCurrentUser()['id'];
        $rows = $this->objDutiesModel->update($data);
        return $rows;
    }
}
