<?php
/**
 * @name ActivityPlugin
 * @desc 用户每次操作，记录本次操作时间
 * @see http://www.php.net/manual/en/class.yaf-plugin-abstract.php
 * @author yangmingfu
 */
class ActivityPlugin extends Yaf_Plugin_Abstract {

	public function routerStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
        //TODO 检查上一次活动时间,并记录本次时间
        $model = new ActivityModel();
	}

}
