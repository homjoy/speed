<?php

namespace Admin\Package\Attendance;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;

/**
 * 打卡相关的 package
 * @package Admin\Package\LeaveList
 * @author guojiezhu@meilishuo.com
 * @since 2015-11-13
 */
class PunchLog extends \Admin\Package\Common\BasePackage {

    //mail的发送地址
    private static $instance = null;

    private function __construct() {
        
    }

    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 获取考勤记录
     * @param type $params
     * @return  //返回列表
     */
    public function getDailyPunchLog($params = array()) {
        $ret = self::getClient()->call('atom', 'punch/get_daily_punch_log', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }

    /**
     * 更新每日的数据
     * @param type $params
     * @return type
     */
    public function updateDailyPunchlog($params = array()) {
        $ret = self::getClient()->call('atom', 'punch/update_daily_punch_log', $params);

        $ret = self::parseRemoteData($ret);
        return $ret;
    }

    /**
     * 更新每日的数据
     * @param type $params
     * @return type
     */
    public function createDailyPunchLog($params = array()) {
        $ret = self::getClient()->call('atom', 'punch/create_daily_punch_log', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
}
