<?php
namespace Joint\Package\Workflow;

/**
 * 工作流相关接口
 * @package Joint\Package\Workflow
 * @author minggeng@meilishuo.com
 * @since 2015-12-16
 */
use Frame\Speed\Lib\Api;

class Workflow{

    private static $instance = null;

    private function __construct() {}

    public static function getInstance(){
        if(is_null(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 新建任务
     */
    public static function taskCreate($params = array()) {
        $workflow = Api::workflow('task/task_create', $params);

        return $workflow;
    }

    /**
     * 处理任务
     */
    public static function processTask($params = array()) {
        $workflow = Api::workflow('task/process_task', $params);

        return $workflow;
    }

    /**
     * 取消任务
     */
    public static function revokeTask($params = array()) {
        $workflow = Api::workflow('task/revoke_task', $params);

        return $workflow;
    }

    /**
     *
     * 与我相关的任务列表 （对审批）
     */
    public static function myRelatedTask($params = array()){
        $workflow = Api::workflow('task/my_related_task', $params);

        return $workflow;
    }

    /**
     *
     *  获取我申请的假
     *
     */
    public static function myTask($params = array()){
        $myLeave = Api::workflow('task/my_task', $params);

        return $myLeave;
    }

    /**
     *
     *  查看已经审批完的流程
     *
     */
    public static function taskProgress($params = array()){
        $approve_info = Api::workflow('task/task_progress', $params);

        return $approve_info;
    }
    /**
     *  查看下一个审批人
     */
    public static function getTaskInfoById($params = array()){
        $approve_leader = Api::workflow('task/get_task_info_by_id', $params);

        return $approve_leader;
    }

    /**
     *  获取任务节点信息，处理人信息及处理信息
     *
     */
    public static function getProcessList($params = array()){
        $process_list = Api::workflow('task/get_process_list', $params);

        return $process_list;
    }

} 
