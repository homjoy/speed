<?php
namespace Admin\Modules\Workflow\Process;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Workflow\TaskType;
use Libs\Util\Format;

class AjaxPorcessActionEdit extends BaseModule {
	
	private $params = array();
	public static $VIEW_SWITCH_JSON = TRUE;

	public function run() {
		if(!$this->_init()) {
			return FALSE;
		}
		
		$actionData = $this->getClient()->call('workflowatom', 'process/process_action_list', array(
			'process_id'	=> $this->params['process_id']
		));
		$actionData = $this->parseApiData($actionData);
		
		$curAid = array_keys($actionData);
		$createInfo = array();
		$updateInfo = array();
		
		if(!empty($this->params['agree_action_id'])) {
			foreach($this->params['agree_action_id'] as $k => $v) {
				if(0 == $v) {
					//新增动作
					if( !empty($this->params['agree_action_name'][$k]) && !empty($this->params['agree_action'][$k]) ) {
						$createInfo[] = array(
							'process_id'		=> $this->params['process_id'],
							'action_name'		=> $this->params['agree_action_name'][$k],
							'action_behavior'	=> $this->params['agree_action'][$k],
							'action_type'		=> 1
						);
					}
				}elseif(in_array($v, $curAid)) {
					//更新动作
					if( $actionData[$v]['action_name'] != $this->params['agree_action_name'][$k] || $actionData[$v]['action_behavior'] != $this->params['agree_action'][$k]) {
						$updateInfo[] = array(
							'id'				=> $v,
							'action_name'		=> $this->params['agree_action_name'][$k],
							'action_behavior'	=> $this->params['agree_action'][$k]
						);
					}
					
					unset($actionData[$v]);
				}
			}
		}
		
		if(!empty($this->params['disagree_action_id'])) {
			foreach($this->params['disagree_action_id'] as $k => $v) {
				if(0 == $v) {
					//新增动作
					if( !empty($this->params['disagree_action_name'][$k]) && !empty($this->params['disagree_action'][$k]) ) {
						$createInfo[] = array(
							'process_id'		=> $this->params['process_id'],
							'action_name'		=> $this->params['disagree_action_name'][$k],
							'action_behavior'	=> $this->params['disagree_action'][$k],
							'action_type'		=> 2
						);
					}
				}elseif(in_array($v, $curAid)) {
					//更新动作
					if( $actionData[$v]['action_name'] != $this->params['disagree_action_name'][$k] || $actionData[$v]['action_behavior'] != $this->params['disagree_action'][$k]) {
						$updateInfo[] = array(
							'id'				=> $v,
							'action_name'		=> $this->params['disagree_action_name'][$k],
							'action_behavior'	=> $this->params['disagree_action'][$k]
						);
					}
					
					unset($actionData[$v]);
				}
			}
		}
		
		$delActionIds = array_keys($actionData);
		
		if( empty($createInfo) && empty($updateInfo) && empty($delActionIds) ) {
			$return = Response::gen_error(10001, '无动作项需要更新');
        	$this->app->response->setBody($return);
			
			return FALSE;
		}
		//var_dump($createInfo, $updateInfo, $delActionIds);exit;
		$multiClient = $this->getMultiClient();
		
		if(!empty($createInfo)) {
			$multiClient->call('workflowatom', 'process/process_action_create', array(
				'data'	=> $createInfo
			), 'createAction');
		}
		
		if(!empty($updateInfo)) {
			$multiClient->call('workflowatom', 'process/process_action_edit', array(
				'data'	=> $updateInfo
			), 'updateAction');
		}
		
		if(!empty($delActionIds)) {
			$multiClient->call('workflowatom', 'process/process_action_delete', array(
				'action_id'	=> implode(',', $delActionIds)
			), 'deleteAction');
		}
		
		$multiClient->callData();
		
		$error_msg = '';
		
		if(!empty($multiClient->createAction['content']['error_msg'])) {
			$error_msg .= $multiClient->createAction['content']['error_msg'] . '<br />';
		}
		
		if(!empty($multiClient->updateAction['content']['error_msg'])) {
			$error_msg .= $multiClient->updateAction['content']['error_msg'] . '<br />';
		}
		
		if(!empty($multiClient->deleteAction['content']['error_msg'])) {
			$error_msg .= $multiClient->deleteAction['content']['error_msg'] . '<br />';
		}
		//var_dump($multiClient->updateAction);return;
		if(empty($error_msg)) {
			$this->app->response->setBody(Response::gen_success('编辑成功'));
		}else {
			$this->app->response->setBody(Response::gen_error(10000, $error_msg));
		}
	}
	
	protected function _init() {
		
		$this->params['process_id'] = isset($this->request->POST['process_id']) ? $this->request->POST['process_id'] : 0;
		
		$this->params['agree_action_id'] = isset($this->request->POST['agree_action_id']) ? $this->request->POST['agree_action_id'] : array();
		$this->params['disagree_action_id'] = isset($this->request->POST['disagree_action_id']) ? $this->request->POST['disagree_action_id'] : array();
		
		$this->params['agree_action_name'] = isset($this->request->POST['agree_action_name']) ? $this->request->POST['agree_action_name'] : array();
		$this->params['agree_action'] = isset($this->request->POST['agree_action']) ? $this->request->POST['agree_action'] : array();
		
		$this->params['disagree_action_name'] = isset($this->request->POST['disagree_action_name']) ? $this->request->POST['disagree_action_name'] : array();
		$this->params['disagree_action'] = isset($this->request->POST['disagree_action']) ? $this->request->POST['disagree_action'] : array();
		
		if(empty($this->params['process_id'])) {
			$return = Response::gen_error(10001, '参数错误');
        	$this->app->response->setBody($return);
			
			return FALSE;
		}
		
		return TRUE;
	}

}