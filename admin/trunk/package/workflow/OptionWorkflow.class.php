<?php

namespace Admin\Package\Workflow;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
/**
 * 获取用户细心
 * @package Admin\Package\UserInfo
 * @author hongzhou@meilishuo.com
 * @since 2015-11-27
 */

class OptionWorkflow extends \Admin\Package\Common\BasePackage {

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
     *
     * 读取流程列表
     * @param type $params
     * @return type
     */
    public  function getProcessList($params = array()){
        $approve_info = self::getClient()->call('workflowapi', 'task/get_process_list', $params);
        $approve_info = $this->parseRemoteData($approve_info);
        return $approve_info;
    }
    /*
     * 读取流程id获取信息
     * @param type $params
     * @return type
     */
    public  function getTaskInfoById($params = array()){
        $nextApprove  = self::getClient()->call('workflowapi', 'task/get_task_info_by_id', $params);
        $nextApprove = $this->parseRemoteData($nextApprove);
        return $nextApprove;
    }
    /*
    * 通过date获取流程id
    * @param type $params
    * @return type
    */
    public  function getTaskIdByDate($params = array()){
        $Approve  = self::getClient()->call('workflowapi', 'task/get_task_ids_by_time', $params);
        $Approve = $this->parseRemoteData($Approve);
        return $Approve;
    }

}
