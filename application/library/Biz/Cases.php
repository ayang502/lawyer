<?php
class Biz_Cases {
    public function __construct() {
        $this->objCasesModel = new CasesModel();
    }
    public function getList($query = array()) {
        $list = $this->objCasesModel->fList($query);
        return Util_Tools::changArrKey($list, 'id');
    }
    public function create($data = array()) {
        if (empty($data)){
            return 0;
        }
        $data['created'] = time();
        $id = $this->objCasesModel->insert($data);
        return $id;

    }
    public function modify($data = array()) {
    
    }
    public function delete($id) {
    
    }
}
