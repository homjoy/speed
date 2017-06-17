<?php

namespace Admin\Package\Hr_leave;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
/**
 * 处理节假日的数据
 * @package Admin\Package\LeaveList
 * @author guojiezhu@meilishuo.com
 * @since 2015-11-05
 */

class WorkingCalendarList extends \Admin\Package\Common\BasePackage {
    //mail的发送地址
    private static $instance = null;
    public static $date_type  = array(
        1 => '工作日',
        2 => '假期'
    );
    public static $work_status  = array(
        0 => '无效',
        1 => '有效'
    );
    private function __construct() {}

    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance = new self();
          
        }
        return self::$instance;
    }


    /**
     * 
     * @param type $params
     * @return  //返回列表
     */
    public  function getCalendarList($params = array()){
        $ret = self::getClient()->call('atom', 'hr_leave/working_calendar_list', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }

    /**
     * 带分页获取数据
     * @param array $params
     * @return bool
     */
    public  function getPageCalendarInfo($params = array()){
        $ret = self::getClient()->call('atom', 'hr_leave/get_working_calendar', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }


    /**
     * 更新数据
     * @param array $params
     * @return bool
     */
    public  function updateCalendarInfo($params = array()){
        $ret = self::getClient()->call('atom', 'hr_leave/update_working_calendar', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }


    /**
     * 创建数据
     * @param array $params
     * @return bool
     */
    public  function createCalendarInfo($params = array()){
        $ret = self::getClient()->call('atom', 'hr_leave/create_working_calendar', $params);

        $ret = self::parseRemoteData($ret);
        return $ret;
    }
  

}
