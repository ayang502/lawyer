<?php
class Util_Tools {
    static public function getCurrentUser() {
        $ua = unserialize(Util_Encode::decrypt($_COOKIE['ua']));
        list($id, $name, $roleid, $mobile, $ip, $time) =  $ua;
        $user['id'] = $id;
        $user['name'] = $name;
        $user['roleid'] = $roleid;
        $user['mobile'] = $mobile;
        $user['ip'] = $ip;
        $user['time'] = $time;
        return $user;
    }
    static function isMobile($mobile) {
        if (preg_match("/^1[34578]{1}\d{9}$/", $mobile)){  
            return true;
        } else {
            return false;
        }  
    }
    static function isMail($mail) {
        if (filter_var($mail, FILTER_VALIDATE_EMAIL)) { 
            return true;
        } else {
            return false;
        }  
    }

    static function changArrKey($array, $key='id'){
        $result = array();
        foreach ($array as $k=>$v) {
            if (!empty($v[$key])) {
                $result[$v[$key]] = $v;
            }
        }
        return $result;
    }
    static function getStatus($biz, $status=null, $html = true) {
        $config = new Yaf_Config_Ini(dirname(__FILE__) . '/../../../conf/status.ini');
        if (!empty($config[$biz])) {
            foreach ($config[$biz] as $k=>$v) {
                $tmp = explode(",", $v);
                $str[$k] = $tmp[0];
                $css[$k] = $tmp[1];
            }
        } else {
            return '';
        }
        if (is_null($status)) {
            return $str;
        }
        if ($html && !is_null($status)) {
            $res = '<span class="label label-mini '.$css[$status].'">' . $str[$status] .'</span>';
            return $res;
        }
        if (!$html && !is_null($status)) {
            $res = $str[$status];
            return $res;
        }
        return $str;
    }
    static function num2zh($num = 0) {
        $res = $num;
        switch($num) {
            case 0 : $res = '0';break;
            case 1 : $res = '一';break;
            case 2 : $res = '二';break;
            case 3 : $res = '三';break;
            case 4 : $res = '四';break;
        }
        return $res;
    }
    static function subZh($content, $len = 50){
        return mb_substr($content, 0, $len, 'utf-8') ; 
    }
    static function getFiles($arr){
        if (empty($arr)) {
            return array();
        }
        foreach ($arr as $k=>$v){
            if ($v['need'] == 1) {
                $need = '必需';
            } else {
                $need = '选需';
            }
            $res[] = $v['name'] .  "(" . $need . ")";
        }
        return $res;
    }
}
