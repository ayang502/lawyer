<?php
/**
 * @name CasesController
 * @author yangmingfu
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class CasesController extends BaseController {
    public $actions = array(
        "ctype" => "actions/Ctype.php",
        "flag" => "actions/Flag.php",
    );
    public function listAction() {
        return TRUE;
    }
    public function nextAction() {
        $request = $this->getRequest();
        $id = $request->getParams('id', 0);
        if (empty($id)) {
            return TRUE; 
        }
        $objCasesBiz = new Biz_Cases();
        $objFlagBiz = new Biz_Flag();
        $list = $objCasesBiz->getList($id)[0];
        if (empty($list) || $list['user_id'] != Util_Tools::getCurrentUser()['id']) {
            return TRUE;
        }
        $files = $objFlagBiz->getList($list['cases_flag']);
        if (empty($files)) {

        }
        $this->getView()->assign('list', $list);
    }
    public function createAction() {
        $objFlagBiz = new Biz_Flag();
        $objCtypeBiz = new Biz_Ctype();
        $objRoleBiz = new Biz_Role();
        $objUserBiz = new Biz_User();
        //案件阶段
        $flagList = $objFlagBiz->getList(array('status'=>1));
        //案件种类
        $ctypeList = $objCtypeBiz->getList(array('status'=>1, 'parentid'=>0));
        //经办、指导律师
        //200105//200106
        $roleList = $objRoleBiz->getList(); 
        $rids[1] = 1;
        foreach ($roleList as $id=>$v) {
            $resourceids = explode(',', $v['resourceid']);
            if (in_array(200105, $resourceids)) {
                $guide_rids[$id] = $id;
                $rids[$id] = $id;
            }
            if (in_array(200106, $resourceids)) {
                $handle_rids[$id] = $id;
                $rids[$id] = $id;
            }
        }
        $userList = $objUserBiz->getList("roleid in (".join(',', $rids).")", true);
        foreach ($userList as $id=>$v) {
            if ($v['roleid'] == 1) {
                $guide_user[$id] = $v;
                $handle_user[$id] = $v;
            }
            if (isset($guide_rids[$v['roleid']])) {
                $guide_user[$id] = $v;
            }
            if (isset($handle_rids[$v['roleid']])) {
                $handle_user[$id] = $v;
            }
        }
        $this->getView()->assign('flagList', $flagList);
        $this->getView()->assign('ctypeList', $ctypeList);
        $this->getView()->assign('guideUser', $guide_user);
        $this->getView()->assign('handleUser', $handle_user);
        return TRUE;
    }
    public function saveAction() {
        $request = $this->getRequest();
        $data = $request->getPost();
        if (!empty($data['consignor_idcode']) && false === Util_Idcard::validation($data['consignor_idcode'])) {
            exit($this->setError(' 委托人身份证号错误'));
        }
        if (!empty($data['party_idcode']) && false === Util_Idcard::validation($data['party_idcode'])) {
            exit($this->setError(' 对方当事人身份证号错误'));
        }
        if (!empty($data['third_idcode']) && false === Util_Idcard::validation($data['third_idcode'])) {
            exit($this->setError(' 第三人身份证号错误'));
        }
        $data['cases_typeid'] = join(',', $data['cases_typeid']);
        $data['user_id'] = Util_Tools::getCurrentUser()['id'];
        $objCasesBiz = new Biz_Cases();
        $id = $objCasesBiz->create($data);
        $this->ajaxResult['data']['type'] = 'save';
        $this->ajaxResult['data']['id'] = $id;
        exit(json_encode($this->ajaxResult));
    }
}
