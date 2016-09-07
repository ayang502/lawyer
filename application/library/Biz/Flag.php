<?php
class Biz_Flag {
    public function __construct() {
        $this->objFlagModel = new FlagModel();
    }
    public function getList($query = array()) {
        $list = $this->objFlagModel->fList($query);
        return Util_Tools::changArrKey($list, 'id');
    }
    public function create($name, $files = array(), $need = array()) {
        $list = $this->objFlagModel->ffName($name);
        if (!empty($list)) {
            return false;
        }
        $data['name'] = $name;
        $data['operator'] = Util_Tools::getCurrentUser()['id'];
        $data['created'] = time();
        foreach ($files as $k=>$v) {
            if (!empty($v)) {
                $file[] = array('name'=>$v, 'need'=>$need[$k]);
            }
        }
        $data['files'] = json_encode($filem, JSON_UNESCAPED_UNICODE);
        $Flagid = $this->objFlagModel->insert($data);
        return $Flagid;
    }
    public function modify($id, $name="", $files = array(), $need = array(), $status = '') {
        $list = $this->objFlagModel->ffId($id);
        if (empty($list)) {
            return false;
        }
        $data['id'] = $id;
        if (!empty($name)) {
            $data['name'] = $name;
        }
        if (!empty($files)) {
            foreach ($files as $k=>$v) {
                if (!empty($v)) {
                    $file[] = array('name'=>$v, 'need'=>$need[$k]);
                }
            }
            $data['files'] = json_encode($file, JSON_UNESCAPED_UNICODE);
        }
        if (!empty($status)) {
            $data['status'] = $status;
        }
        $data['operator'] = Util_Tools::getCurrentUser()['id'];
        $rows = $this->objFlagModel->update($data);
        return $rows;
    }
    public function delete($id) {
        $data['operator'] = Util_Tools::getCurrentUser()['id'];
        $data['id'] = $id;
        $data['status'] = '-1';
        $rows = $this->objFlagModel->update($data);
        return $rows;
    }
}
