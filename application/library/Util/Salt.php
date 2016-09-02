<?php
class Util_Salt {
    static $charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHKLMNPRSTUVWXYZ123456789!@#$%^&*()_+{}:""';
    static $codelen = 6;
    static $passwdlen = 8;

    static public function genSalt() {
        $_len = strlen(self::$charset)-1;
        $salt = '';
        for ($i=0; $i<self::$codelen; $i++) {
            $salt .= self::$charset[mt_rand(0, $_len)];
        }
        return $salt;
    }

    static public function genPasswd($passwd, $salt = ''){
        if (empty($salt)){  
            $salt = self::genSalt(); 
        }
        $passwd = md5($passwd . $salt);
        return [$passwd, $salt];
    }
    static public function isPasswd($pass){
        return true;
    }
}
