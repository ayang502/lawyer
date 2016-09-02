<?php
class Biz_Login {
    public function __construct() {

    }
    public function login($arrInfo) {
        list($mobile, $passwd) = $arrInfo;
        $objUserModel = new UserModel();
        $res = $objUserModel->ffMObile($mobile);
        if (empty($res)) {
            return false;
        }
        if ($res['status'] != 1) {
            return false;
        }
        //验证密码
        if (Util_Salt::genPasswd($passwd, $res['salt'])[0] != $res['password']) {
            return false;
        }
        //写Cookie
        $data[] = $res['id'];
        $data[] = $res['name'];
        $data[] = $res['roleid'];
        $data[] = $res['mobile'];
        $data[] = Util_Uip::getIP();
        $data[] = time();
        $ua = Util_Encode::encrypt(serialize($data));
        $res = setcookie('ua', $ua, time()+86400*10, '/');
        return $res;
    }
    public function isLogin() {
        if (!isset($_COOKIE['ua']) || empty($_COOKIE['ua'])) {
            return false;
        }
        $ua = unserialize(Util_Encode::decrypt($_COOKIE['ua']));
        if (!$ua) {
            return false;
        }
        list($id, $name, $roleid, $mobile, $ip, $time) =  $ua;
        $cip = Util_Uip::getIP();
        if ($ip != $cip) {
            return false;
        }
        $objUserModel = new UserModel();
        $res = $objUserModel->ffId($id);
        if ($res['roleid'] != $roleid || $res['name'] != $name || $res['mobile'] != $mobile) {
            return false;
        }
        return true;
    }
}
