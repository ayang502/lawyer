<?php
class Biz_Casesextra {
    public function __construct() {
        $this->objCasesextraModel = new CasesextraModel();
    }
    public function getList($query = array()) {
        if (!empty($query)) {
            $this->objCasesextraModel->wnereAnd($query);
        }
        $list = $this->objCasesextraModel->fList($query);
        return Util_Tools::changArrKey($list, 'id');
    }
    public function create($data = array()) {
        if (empty($data)){
            return 0;
        }
        $cid = $data['cases_id'];
        $flag = $data['cases_flag'];
        $this->objCasesextraModel->whereAnd(array('cases_id'=>$cid, 'cases_flag'=>$flag));
        $list = $this->objCasesextraModel->fList();
        if (!empty($list)) {
            return false;
        }

        $data['created'] = time();
        $id = $this->objCasesextraModel->insert($data);
        return $id;
    }
    public function modify($data = array()) {
    
    }
    public function delete($id) {
    
    }
}
