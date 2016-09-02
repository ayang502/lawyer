<?php
/**
 * @name SysController
 * @author yangmingfu
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class SysController extends BaseController {
    public $actions = array (
        "user" => "actions/User.php",
        "role" => "actions/Role.php",
        "menu" => "actions/Menu.php",
        "duties" => "actions/Duties.php",
        "proclamation" => "actions/Proclamation.php",
    );
	public function SysAction() {
        return TRUE;
	}
}
