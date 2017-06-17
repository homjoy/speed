<?php 
namespace WorkFlowApi\Modules\Task;

/**
 * 我创建的任务列表
 * @author jingjingzhang@meilishuo.com
 * @since 2015-07-31
 */

use WorkFlowApi\Modules\Common\BaseModule;
use WorkFlowApi\Package\Common\Response;

class MyTask extends BaseModule {

	private $params;

	public function run() {

		$this->_init();

		//参数校验
		if($this->post()->hasError()){
			$return = Response::gen_error(10001, '', $this->post()->getErrors());
			return $this->app->response->setBody($return);
		}

		if (empty($this->params['user_id'])) {
			$return = Response::gen_error(10001);
        	return $this->app->response->setBody($return);
		}

		$queryParams['user_id'] = $this->params['user_id'];
		if (!empty($this->params['tasktype_id'])) {
			$queryParams['tasktype_id'] = $this->params['tasktype_id'];
		}
		
		$result = self::getClient()->call('workflowatom', 'process/task_list', $this->params);
		$result = $this->parseApiData($result);

		if (empty($result) && is_array($result)) {
			$this->app->response->setBody(Response::gen_success($result));
		} else if (!empty($result)) {
			$userIds = $user = $user_map = array();
			foreach ($result as $k => $v) {
				if ($v['current_user_id'] != 0) {
					$userIds[] = $v['current_user_id'];	
				}
			}

			if (!empty($userIds)) {
				$userIds = array_unique($userIds);
				$user = $this->getUserInfo(array('user_id' => implode(',', $userIds), 'status' => array(1,2,3)));
			}

			if (!empty($user)) {
				foreach ($user as $k => $v) {
					$user_map[$v['user_id']] = $v['name_cn'];
				}
			}

			foreach ($result as $k => $v) {
				if ($v['current_user_id'] != 0 && isset($user_map[$v['current_user_id']])) {
					$v['current_user_name'] = $user_map[$v['current_user_id']];
					$result[$k] = $v;
				}
			}

			$this->app->response->setBody(Response::gen_success($result));
		}
	}

	private function getUserInfo($param) {

		$ret = self::getClient()->call('atom', 'user/user_info_get', $param);
		$ret = $this->parseApiData($ret);

		return $ret;
	}

	private function _init() {

		$this->rules = array(
			'user_id' => array(
				'required'   => TRUE,
				'type'       => 'integer',
				'maxLength'  => 11,
			),
			'tasktype_id' => array(
				'type' => 'integer',
			),
		);

		$this->params = $this->post()->safe();
	}
}