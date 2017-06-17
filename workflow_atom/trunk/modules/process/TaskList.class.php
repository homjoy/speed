<?php 
namespace WorkFlowAtom\Modules\Process;

use WorkFlowAtom\Modules\Common\BaseModule;
use WorkFlowAtom\Package\Common\Response;
use WorkFlowAtom\Package\Process\Task;
use Libs\Util\Format;

/**
 * 任务列表
 * @author jingjingzhang@meilishuo.com
 * @date 2015-7-22
 */

class TaskList extends BaseModule {

	protected $task;
	protected $errors = array();
	private $sample;

	public function run() {

		$this->_init();
		
		$this->sample = Task::getInstance()->getFields();
		$this->sample['create_time'] = '';

		//参数校验
		if ($this->post()->hasError()) {
			$return = Response::gen_error(10001, '', $this->errors);
        	return $this->app->response->setBody($return);
		}

		$queryParams = array();
		if (!empty($this->task['task_id'])) {
			$queryParams['task_id'] = $this->task['task_id'];
		}
		if (!empty($this->task['tasktype_id'])) {
			$queryParams['tasktype_id'] = $this->task['tasktype_id'];
		}
		if (!empty($this->task['user_id'])) {
			$queryParams['user_id'] = $this->task['user_id'];
		}
		if (!empty($this->task['task_name'])) {
			$queryParams['task_name'] = $this->task['task_name'];
		}
		if (!empty($this->task['task_content'])) {
			$queryParams['task_content'] = $this->task['task_content'];
		}
		if (!empty($this->task['current_user_id'])) {
			$queryParams['current_user_id'] = $this->task['current_user_id'];
		} else if (isset($this->task['flag']) && $this->task['flag'] == 1) {
			$queryParams['current_user_id'] = 0;
		}
		if (!empty($this->task['status'])) {
			$queryParams['status'] = $this->task['status'];
		}
		if (!empty($this->task['current_node_id'])) {
			$queryParams['current_node_id'] = $this->task['current_node_id'];
		} 
		// if (!empty($this->task['current_depart_id'])) {
		// 	$queryParams['current_depart_id'] = $this->task['current_depart_id'];
		// } 	
		if (!empty($this->task['start_time'])) {
			$queryParams['start_time'] = $this->task['start_time'];
		}
		if (!empty($this->task['end_time'])) {
			$queryParams['end_time'] = $this->task['end_time'];
		}
		if (!empty($this->task['start_create_time'])) {
			$queryParams['start_create_time'] = $this->task['start_create_time'];
		}
		if (!empty($this->task['end_create_time'])) {
			$queryParams['end_create_time'] = $this->task['end_create_time'];
		}
		if (!empty($this->task['order'])) {
			$queryParams['order'] = $this->task['order'];
		}
		if (!empty($this->task['count'])) {
			$queryParams['count'] = $this->task['count'];
		}
		if (!empty($this->task['page_flag'])) {
			$queryParams['page_flag'] = $this->task['page_flag'];
			if (empty($this->task['page'])) {
				$this->task['page'] = 1;
			}
			if (empty($this->task['limit'])) {
				$this->task['limit'] = 100;
			}
		}

		$result = Task::getInstance()->getDataList($queryParams, $this->task['page'], $this->task['limit']);

		if ($result === FALSE) {
        	$return = Response::gen_error(50001);
        }elseif (empty($result)) {
        	$return = Response::gen_error(50002);
        }else{
        	if(isset($this->task['count']) && $this->task['count'] == 1) { 
                $return =  Response::gen_success($result);
            } else {
            	$return = Response::gen_success(Format::outputData($result, $this->sample, TRUE));
            }	
        }

        $this->app->response->setBody($return);
	}

	/**
     * 参数初始化
     */
	private function _init() {

		$this->rules = array(
			'task_id'         => array(
				'type' => 'multiId',
			),
			'tasktype_id'     => array(
				'type' => 'integer',
			),
			'user_id'         => array(
				'type' => 'integer',
			),
			'task_name'       => array(
				'type' => 'string',
			),
			'task_content'    => array(
				'type' => 'string',
			),
			'current_user_id' => array(
				'type'    => 'integer',
				'default' => 0,
			),
			'status' => array(
				'type' => 'multiId',
			),
			'current_node_id' => array(
				'type' => 'integer',
			),
			// 'current_depart_id' => array(
			// 	'type'    => 'integer',
			// ),
			'flag' => array(
				'type'    => 'integer',
				'default' => 0,
			),
			'start_time'  => array(
				'type'       => 'datetime',
			),
			'end_time'    => array(
				'type'       => 'datetime',
			),
			'start_create_time'  => array(
				'type'       => 'datetime',
			),
			'end_create_time'    => array(
				'type'       => 'datetime',
			),
			'limit' => array(
				'type'    => 'integer',
				'default' => 100,
			),
			'page' => array(
				'type'    => 'integer',
				'default' => 1,
			),
			'count' => array(
				'type' => 'integer',
			),
			'page_flag' => array( //是否需要分页
				'type' => 'integer',
			)
		);

		$this->task = $this->post()->safe();
		$this->errors = $this->post()->getErrors();

		$this->task['order'] = isset($this->request->POST['order']) ? $this->request->POST['order'] : '';
	}
}