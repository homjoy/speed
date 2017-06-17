<?php

namespace Admin\Package\Hr_leave;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
/**
 * 获取请假相关信息的package
 * @package Admin\Package\LeaveList
 * @author guojiezhu@meilishuo.com
 * @since 2015-11-05
 */

class LeaveList extends \Admin\Package\Common\BasePackage {
    //mail的发送地址
    private static $instance = null;
    private function __construct() {}

    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance = new self();
          
        }
        return self::$instance;
    }


    /**
     * 获取邮件组的列表
     * @param type $params
     * @return  //返回列表
     */
    public  function getLeaveList($params = array()){
        $ret = self::getClient()->call('atom', 'hr_leave/get_leave_list', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function updateLeaveInfo($params = array()){
        $ret = self::getClient()->call('atom', 'hr_leave/leave_update_info', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function getLeaveLength($params = array()){
        $ret = self::getClient()->call('atom', 'hr_leave/calculation_leave_days', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function getLeaveAnnual($params = array()){
        $ret = self::getClient()->call('atom', 'hr_leave/leave_annual', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function LeaveAbortion($params = array()){
        $ret = self::getClient()->call('atom', 'hr_leave/leave_abortion', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function getLeaveAbsence($params = array()){
        $ret = self::getClient()->call('atom', 'hr_leave/leave_absence', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function getLeaveDetection($params = array()){
        $ret = self::getClient()->call('atom', 'hr_leave/leave_detection', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function getLeaveFuneral($params = array()){
        $ret = self::getClient()->call('atom', 'hr_leave/leave_funeral', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function getLeaveMarital($params = array()){
        $ret = self::getClient()->call('atom', 'hr_leave/leave_marital', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function getLeaveMaternity($params = array()){
    $ret = self::getClient()->call('atom', 'hr_leave/leave_maternity', $params);
    $ret = self::parseRemoteData($ret);
    return $ret;
    }
    public  function getLeavePaidSick($params = array()){
        $ret = self::getClient()->call('atom', 'hr_leave/leave_paid_sick', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function getLeavePaternity($params = array()){
        $ret = self::getClient()->call('atom', 'hr_leave/leave_paternity', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function getLeaveSick($params = array()){
        $ret = self::getClient()->call('atom', 'hr_leave/leave_sick', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
}
