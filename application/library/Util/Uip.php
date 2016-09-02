<?php
class Util_Uip {
    static public function getIP() {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip  = $ips[0];
        } else if (!empty($_SERVER['HTTP_CLIENTIP'])) {
            $ip = $_SERVER['HTTP_CLIENTIP'];
        } else if (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = '127.0.0.1';
        }
        return self::ip2num($ip);
    }
    static public function ip2num($strIP) {
        return sprintf('%u', ip2long($strIP));
    }
}
