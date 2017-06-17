<?php 
namespace WorkFlowApi\Modules\Task;

/**
 * 我审批的任务列表
 * @author jingjingzhang@meilishuo.com
 * @since 2015-08-03
 */

use WorkFlowApi\Modules\Common\BaseModule;
use WorkFlowApi\Package\Common\Response;

class MyApprove extends BaseModule {

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

		//通过 current_user_id 查询
		$result = self::getClient()->call('workflowatom', 'process/task_list', array(
			'current_user_id' => $this->params['user_id'],
		));
		$result = $this->parseApiData($result);

		if ($result !== FALSE) {
			$this->app->response->setBody(Response::gen_success($result));
		}
	}

	private function _init() {

		$this->rules = array(
			'user_id' => array(
				'required'  => TRUE,
				'type'      => 'integer',
				'maxLength' => 11,
			), 
		);

		$this->params = $this->post()->safe();
	}
}