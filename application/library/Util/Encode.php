<?php
class Util_Encode {
    static $key;
    static $iv;
    function __construct ($iv=0) {
        $config = new Yaf_Config_Ini(dirname(__FILE__) . '/../../../conf/config.ini');
        self::$key = $config['encode']['key'];
        $iv = strlen(self::$key);
        self::$iv = $iv;
    }
    static public function encrypt($str) {
        $size = mcrypt_get_block_size(MCRYPT_DES, MCRYPT_MODE_CBC);
        $str = self::pkcs5Pad($str, $size);
        return strtoupper(bin2hex(mcrypt_cbc(MCRYPT_DES, self::$key, $str, MCRYPT_ENCRYPT, self::$iv)));
    }

    static public function decrypt($str) {
        $strBin = self::hex2bin(strtolower($str));
        $str = mcrypt_cbc(MCRYPT_DES, self::$key, $strBin, MCRYPT_DECRYPT, self::$iv);
        $str = self::pkcs5Unpad($str);
        return $str;
    }

    static private function hex2bin($hexData) {
        $binData = "";
        for($i = 0; $i < strlen($hexData); $i += 2) {
            $binData .= chr(hexdec(substr($hexData, $i, 2)));
        }
        return $binData;
    }

    static private function pkcs5Pad($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    static private function pkcs5Unpad($text) {
        $pad = ord($text {strlen($text) - 1});
        if($pad > strlen($text))
            return false;
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad)
            return false;
        return substr($text, 0, - 1 * $pad);
    }
}
