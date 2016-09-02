<?php
/**
 * @name ActivityModel
 * @desc Activity 上一次活动时间，记录redis
 * @author yangmingfu
 */
class ActivityModel {
    var $objRedis;
    public function __construct() {
        $config = new Yaf_Config_Ini(dirname(__FILE__) . '/../../conf/config.ini');
    }   
    
    public function selectActivity() {
        return 'Hello World!';
    }

    public function insertActivity($arrInfo) {
        return true;
    }
}
