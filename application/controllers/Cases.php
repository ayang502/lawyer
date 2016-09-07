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
        $objCasesBiz = new Biz_Cases();
        $objFlagBiz = new Biz_Flag();
        $objUserBiz = new Biz_User();
        $objCtypeBiz = new Biz_Ctype();
        $objExtraBiz = new Biz_Casesextra();
        $list = $objCasesBiz->getList();
        foreach ($list as $k=>$v) {
            $return = $v;
            $return['created'] = date('Y-m-d H:i:s', $v['created']);
            $uids[$v['user_id']] = $v['user_id'];
            $uids[$v['guide_user']] = $v['guide_user'];
            $uids[$v['handle_user']] = $v['handle_user'];
            $flags[$v['cases_flag']] = $v['cases_flag'];
            $return['type'] = explode(',', $v['cases_typeid']);
            foreach ($return['type'] as $type) {
                $typeid[$type] = $type;
            }
            $timeline = $objExtraBiz->getList(array('cases_flag'=>$v['cases_flag'], 'cases_id'=>$v['id']));
            foreach ($timeline as $k=>$v) {
                $return['files'] = $v['files'];
                $return['timeline'] = json_decode($v['timeline'], true);
            }
            $result[] = $return;
        }
        $users = $objUserBiz->getList(join(',', $uids));
        $flag = $objFlagBiz->getList(join(',', $flags));
        $types = $objCtypeBiz->getList(join(',', $typeid));
        $this->getView()->assign('result', $result);
        $this->getView()->assign('users', $users);
        $this->getView()->assign('flag', $flag);
        $this->getView()->assign('types', $types);
        return TRUE;
    }
    public function infoAction() {
        $request = $this->getRequest();
        $params = $request->getParams();
        if (!empty($params) && !empty($params['id'])) {
            $id = $params['id'];
        } else {
            return TRUE;
        }

        $objCasesBiz = new Biz_Cases();
        $objFlagBiz = new Biz_Flag();
        $list = $objCasesBiz->getList($id)[$id];

        $objCasesBiz = new Biz_Cases();
        $objFlagBiz = new Biz_Flag();
        $objUserBiz = new Biz_User();
        $objCtypeBiz = new Biz_Ctype();
        $objExtraBiz = new Biz_Casesextra();

        $return = $objCasesBiz->getList($id)[$id];
        $return['created'] = date('Y-m-d H:i:s', $return['created']);
        $uids[$return['user_id']] = $return['user_id'];
        $uids[$return['guide_user']] = $return['guide_user'];
        $uids[$return['handle_user']] = $return['handle_user'];
        $flags[$return['cases_flag']] = $return['cases_flag'];
        $return['type'] = explode(',', $return['cases_typeid']);
        foreach ($return['type'] as $type) {
            $typeid[$type] = $type;
        }
        $extra = $objExtraBiz->getList(array('cases_id'=>$id))[$id];
        $flags[$extra['cases_flag']] = $extra['cases_flag'];
        $types[] = $extra['cases_type'];
        $extra['timeline'] = json_decode($extra['timeline'], true);
        $extra['operator'] = json_decode($extra['operator'], true);

        $users = $objUserBiz->getList(join(',', $uids));
        $flag = $objFlagBiz->getList(join(',', $flags));
        $types = $objCtypeBiz->getList(join(',', $typeid));

        $this->getView()->assign('result', $return);
        $this->getView()->assign('users', $users);
        $this->getView()->assign('flag', $flag);
        $this->getView()->assign('types', $types);
        $this->getView()->assign('extra', $extra);
        return TRUE;
    }
    public function nextAction() {
        $request = $this->getRequest();
        $params = $request->getParams();
        if (!empty($params) && !empty($params['id'])) {
            $id = $params['id'];
        } else {
            return TRUE;
        }
        $objCasesBiz = new Biz_Cases();
        $objFlagBiz = new Biz_Flag();
        $list = $objCasesBiz->getList($id);
        $list = $list[$id];
        if (empty($list) || $list['user_id'] != Util_Tools::getCurrentUser()['id']) {
            return TRUE;
        }
        $info = $objFlagBiz->getList($list['cases_flag'])[$list['cases_flag']];
        if (!empty($info)) {
            $info['files'] = json_decode($info['files'], true);
        }
        $this->getView()->assign('list', $list);
        $this->getView()->assign('info', $info);
    }
    public function submitAction() {
        $request = $this->getRequest();
        $params = $request->getPost();
        if (!empty($params) && !empty($params['casesid'])) {
            $id = $params['casesid'];
        } else {
            return TRUE;
        }
        $objCasesBiz = new Biz_Cases();
        $objCasesExtra = new Biz_Casesextra();
        $list = $objCasesBiz->getList($id);
        $list = $list[$id];
        if (empty($list) || $list['user_id'] != Util_Tools::getCurrentUser()['id']) {
            return TRUE;
        }
        $data['cases_id'] = $params['casesid'];
        $data['cases_flag'] = $list['cases_flag'];
        $data['files'] = $params['files'];
        foreach ($params['times'] as $k=>$v) {
            if (empty($v)){
                continue;
            }
            if (empty($params['date'][$k])) {
                continue;
            }
            $timeline[] = array('name'=>$v, 'time'=>$params['date'][$k]);
        }
        if (!empty($timeline)) {
            $data['timeline'] = json_encode($timeline, JSON_UNESCAPED_UNICODE);
        }
        if (!empty($params['operator'])){
            $data['operator'] = json_encode($params['operator'], JSON_UNESCAPED_UNICODE);
        }
        $id = $objCasesExtra->create($data);
        if ($id > 0) {
            exit(json_encode($this->ajaxResult));
        } else if($id === false) {
            exit($this->setError(' 该案件节点已存在')); 
        }
    }
    public function uploadAction() {
        $caseid = $this->getRequest()->getPost('id');
        $caseFlag = $this->getRequest()->getPost('f');
        if ($_FILES["files"]["error"] > 0) {
            exit($this->setError($_FILES["files"]["error"]));
        } else {
            $name = $_FILES["files"]["name"];
            $res = Util_Files::upload($_FILES['files']['tmp_name'], $caseid, $caseFlag, $name);
            if ($res){
                $this->ajaxResult['data'] = Util_Tools::getCurrentUser()['id'] . "/{$caseid}/{$case_flag}/{$name}";
            } else {
                exit(json_encode(array('error'=>'上传失败')));
            }
            exit(json_encode($this->ajaxResult));
        }

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
    public function modifyAction() {
        $request = $this->getRequest();
        $params = $request->getParams();
        if (!empty($params) && !empty($params['id'])) {
            $id = $params['id'];
        } else {
            return TRUE;
        }

        $objCasesBiz = new Biz_Cases();
        $objFlagBiz = new Biz_Flag();
        $list = $objCasesBiz->getList($id)[$id];

        $objCasesBiz = new Biz_Cases();
        $objFlagBiz = new Biz_Flag();
        $objUserBiz = new Biz_User();
        $objCtypeBiz = new Biz_Ctype();

        $return = $objCasesBiz->getList($id)[$id];
        $return['created'] = date('Y-m-d H:i:s', $return['created']);
        $uids[$return['user_id']] = $return['user_id'];
        $uids[$return['guide_user']] = $return['guide_user'];
        $uids[$return['handle_user']] = $return['handle_user'];
        $flags[$return['cases_flag']] = $return['cases_flag'];
        $return['type'] = explode(',', $return['cases_typeid']);
        foreach ($return['type'] as $type) {
            $typeid[$type] = $type;
        }

        $users = $objUserBiz->getList(join(',', $uids));
        $flag = $objFlagBiz->getList(join(',', $flags));
        $types = $objCtypeBiz->getList(join(',', $typeid));

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

        $this->getView()->assign('result', $return);
        $this->getView()->assign('users', $users);
        $this->getView()->assign('flag', $flag);
        $this->getView()->assign('types', $types);
        $this->getView()->assign('guideuser', $guide_user);
        $this->getView()->assign('handleuser', $handle_user);
        return TRUE;
    }

}
