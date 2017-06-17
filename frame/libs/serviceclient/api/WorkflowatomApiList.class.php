<?php

namespace Libs\Serviceclient\Api;

/**
 * 记录api的请求方式和服务模块
 * @author zx
 */
class WorkflowatomApiList extends \Libs\Serviceclient\Api\ApiList {

    protected static $apiList = array(
        //测试
        'a/example' => array('service' => 'workflowatom', 'method' => 'GET', 'opt' => array('timeout' => 3)),

        //菜单
        'tree/tree' => array('service' => 'workflowatom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'tree/treeAdd' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'tree/tree_add' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),

        //菜单权限
        'treerole/role' => array('service' => 'workflowatom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'treerole/roleAdd' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'treerole/role_add' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'treerole/role_by_param' => array('service' => 'workflowatom', 'method' => 'GET', 'opt' => array('timeout' => 3)),

        //用户角色
        'user/role_info_create' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'user/role_info_list' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'user/role_info_update' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'user/role_info_get' => array('service' => 'workflowatom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'user/user_role_map_create' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'user/user_role_map_update' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'user/user_role_map_list' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'user/user_role_map_delete' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'user/user_agency_update' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'user/user_agency_create' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'user/user_agency_delete' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'user/user_agency_list' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),

        //工作流类型		
		'process/getProcessInfoById' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'process/processActionList' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'process/processRuleDelete' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'process/taskTypeEdit' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'process/getTypeInfoById' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'process/processNodeCreate' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'process/processRuleEdit' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'process/taskTypeList' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'process/processActionCreate' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'process/processNodeEdit' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'process/processRuleList' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'process/processActionDelete' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'process/processNodeList' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'process/processActionEdit' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'process/processRuleCreate' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		
		'process/get_process_info_by_id' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'process/process_action_list' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'process/process_rule_delete' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'process/task_type_edit' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'process/get_type_info_by_id' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'process/process_node_create' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'process/process_rule_edit' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'process/task_type_list' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'process/process_action_create' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'process/process_node_edit' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'process/process_rule_list' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'process/process_action_delete' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'process/process_node_list' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'process/process_action_edit' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'process/process_rule_create' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),

        //任务  
        'process/task_create' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)), 
        'process/task_update' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),    
        'process/task_list' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'process/multi_task_create' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'process/progress_create' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'process/progress_list' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'process/data_sync' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),

        'tree/treePermissions' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'tree/treePermissionsAdd' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		
		'tree/tree_permissions' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'tree/tree_permissions_add' => array('service' => 'workflowatom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        
        'treeuserrole/user_role_by_uid' => array('service' => 'workflowatom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        
        'tree/tree_by_uid' => array('service' => 'workflowatom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'tree/tree_by_param' => array('service' => 'workflowatom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'tree/tree_permissions_by_param' => array('service' => 'workflowatom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        
    );
}
