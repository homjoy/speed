<?php 
namespace WorkFlowAtom\Modules\Process;

use WorkFlowAtom\Modules\Common\BaseModule;
use WorkFlowAtom\Package\Common\Response;
use WorkFlowAtom\Package\Process\Task;
use WorkFlowAtom\Package\Process\Progress;
use Libs\Util\Format;

/**
 * 任务更新
 * @author jingjingzhang@meilishuo.com
 * @date 2015-7-31
 */

class TaskUpdate extends BaseModule {

	protected $params;
	private $sample;

	public function run() {

		if(!$this->_init()) {
			return FALSE;
		}

        if ( empty($this->params['taskParams']['task_id']) ) {
        	$return = Response::gen_error(10001);
        	return $this->app->response->setBody($return);
        }

		try {

			Task::getInstance()->beginTran();
			
			$result = Task::getInstance()->update($this->params['taskParams']);
			
			if (empty($result)) {
				throw new \Exception('task 信息更新失败');
			}
			
			if(!empty($this->params['progressParams'])) {
				if(empty($this->params['progressParams']['task_id'])) {
					$this->params['progressParams']['task_id'] = $this->params['taskParams']['task_id'];
				}
				
				$progress_result = Progress::getInstance()->insert($this->params['progressParams']);
				
				if(empty($progress_result)) {
					throw new \Exception('progress 信息更新失败');
				}
			}
			
			Task::getInstance()->commit();
		} catch(\Exception $e) {

			Task::getInstance()->rollback();
			
			$return =  Response::gen_error(50001, $e->getMessage());
			return $this->app->response->setBody($return);
		}
		
        if ($result === FALSE) {
			$return =  Response::gen_error(50001);
		} else if (empty($result)) {
			$return = Response::gen_error(50004);
		} else {
			$return = Response::gen_success($result);
		}

		$this->app->response->setBody($return);
	}

	private function _init() {

		//$this->rules = array(
		//	'task_id' => array(
		//		'required'   => TRUE,
		//		'type'       => 'integer',
		//		'maxLength'  => 5,
		//	),
		//	/*'tasktype_id' => array(
		//		'type'       => 'integer',
		//		'maxLength'  => 5,
		//	),
		//	'user_id' => array(
		//		'type'       => 'integer',
		//		'maxLength'  => 11,
		//	),
		//	'task_name' => array(
		//		'type'       => 'string',
		//		'maxLength'  => 100,
		//	),
		//	'task_content' => array(
		//		'type'       => 'string',
		//	),*/
		//	'current_user_id' => array(
		//		'type'       => 'integer',
		//	),
		//	'status' => array(
		//		'type'    => 'integer',
		//	),
		//	'current_node_id' => array(
		//		'type'    => 'integer',
		//	),
		//	//progress
		//	'process_id' => array(
		//		'type'    => 'integer',
		//	),
		//	'action_type' => array(
		//		'type'    => 'integer',
		//	),
		//	'progress_content' => array(
		//		'type'    => 'string',
		//	),
		//	'operator' => array(
		//		'type'    => 'integer',
		//	),
		//);
		//
		//$this->task = $this->post()->safe();
		//$this->errors = $this->post()->getErrors();
		
		$this->params['taskParams'] =  isset($this->request->POST['taskParams']) ? $this->request->POST['taskParams'] : array();
		$this->params['progressParams'] =  isset($this->request->POST['progressParams']) ? $this->request->POST['progressParams'] : array();
		
		if(empty($this->params['taskParams'])) {
			
			$return = Response::gen_error(10001, 'task 参数为空');
        	$this->app->response->setBody($return);
			
			return FALSE;
		}
		
		return TRUE;
	}
}