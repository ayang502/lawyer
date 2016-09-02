<?php
class Util_Idcard {
    static function validation($id_card) {
        if(strlen($id_card) == 18) {
            return self::idcard_checksum18($id_card);
        } else if((strlen($id_card) == 15)) {
            $id_card = self::idcard_15to18($id_card);
            return self::idcard_checksum18($id_card);
        } else {
            return false;
        }
    }
    static function idcard_verify_number($idcard_base) {
        if(strlen($idcard_base) != 17) {
            return false;
        }
        //加权因子
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        //校验码对应值
        $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        $checksum = 0;
        for ($i = 0; $i < strlen($idcard_base); $i++) {
            $checksum += substr($idcard_base, $i, 1) * $factor[$i];
        }
        $mod = $checksum % 11;
        $verify_number = $verify_number_list[$mod];
        return $verify_number;
    }
    static function idcard_15to18($idcard){
        if (strlen($idcard) != 15) {
            return false;
        } else {
            if (array_search(substr($idcard, 12, 3), array('996', '997', '998', '999')) !== false) {
                $idcard = substr($idcard, 0, 6) . '18'. substr($idcard, 6, 9);
            } else {
                $idcard = substr($idcard, 0, 6) . '19'. substr($idcard, 6, 9);
            }
        }
        $idcard = $idcard . self::idcard_verify_number($idcard);
        return $idcard;
    }
    static function idcard_checksum18($idcard){
        if (strlen($idcard) != 18) {
            return false;  
        }
        $idcard_base = substr($idcard, 0, 17);
        if (self::idcard_verify_number($idcard_base) != strtoupper(substr($idcard, 17, 1))) {
            return false;
        } else {
            return true;
        }
    }
}
