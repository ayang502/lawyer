<?php
class Biz_Role {
    public function __construct() {
        $this->objRoleModel = new RoleModel();
    }
    public function getList($query = array()) {
        $list = $this->objRoleModel->fList($query);
        return Util_Tools::changArrKey($list, 'id');
    }
    public function create($name, $ids = array()) {
        $list = $this->objRoleModel->ffName($name);
        if (!empty($list)) {
            return false;
        }
        $data['name'] = $name;
        $data['resourceid'] = join(',', $ids);
        $data['operator'] = Util_Tools::getCurrentUser()['id'];
        $data['created'] = time();
        $roleid = $this->objRoleModel->insert($data);
        return $roleid;
    }
    public function modify($id, $name, $ids = array()) {
        $list = $this->objRoleModel->ffId($id);
        if (empty($list)) {
            return false;
        }
        if (!empty($list) && $list['id'] != $id) {
            return false;
        }
        $data['id'] = $id;
        $data['name'] = $name;
        $data['resourceid'] = join(',', $ids);
        $data['operator'] = Util_Tools::getCurrentUser()['id'];
        $rows = $this->objRoleModel->update($data);
        return $rows;
    }
}
