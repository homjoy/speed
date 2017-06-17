<?php

namespace Libs\Serviceclient\Api;

/**
 * 记录workflow_api中api的请求方式和服务模块
 * @author jingjingzhang@meilishuo.com
 * @since 2015-08-20
 */

class WorkflowapiApiList extends \Libs\Serviceclient\Api\ApiList {

	protected static $apiList = array(

		// 任务处理
		'task/task_create' => array('service' => 'workflowapi', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'task/accept_task' => array('service' => 'workflowapi', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'task/process_task' => array('service' => 'workflowapi', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'task/revoke_task' => array('service' => 'workflowapi', 'method' => 'POST', 'opt' => array('timeout' => 3)),

		// 任务展示
		'task/get_task_info_by_id' => array('service' => 'workflowapi', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'task/get_task_process_info_by_id' => array('service' => 'workflowapi', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'task/my_related_task' => array('service' => 'workflowapi', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'task/my_task' => array('service' => 'workflowapi', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'task/task_progress' => array('service' => 'workflowapi', 'method' => 'GET', 'opt' => array('timeout' => 3)),
		'task/task_type_list' => array('service' => 'workflowapi', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'task/get_process_list' => array('service' => 'workflowapi', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'task/get_task_ids_by_time' => array('service' => 'workflowapi', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'task/search_task' => array('service' => 'workflowapi', 'method' => 'POST', 'opt' => array('timeout' => 3)),

		// 脚本名字获取
		'task/get_action_script_name' => array('service' => 'workflowapi', 'method' => 'POST', 'opt' => array('timeout' => 3)),
	);
}