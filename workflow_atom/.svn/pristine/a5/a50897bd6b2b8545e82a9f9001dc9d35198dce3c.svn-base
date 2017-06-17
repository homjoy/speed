<?php 
namespace WorkFlowAtom\Modules\Process;

use WorkFlowAtom\Modules\Common\BaseModule;
use WorkFlowAtom\Package\Common\Response;
use WorkFlowAtom\Package\Process\Progress;
use Libs\Util\Format;

/**
 * 任务处理流程列表
 * @author jingjingzhang@meilishuo.com
 * @date 2015-08-02
 */

class ProgressList extends BaseModule {

	protected $progress;
	protected $errors = array();
	private $sample;

	public function run() {

		$this->_init();

		$this->sample = Progress::getInstance()->getFields();
		$this->sample['create_time'] = '';

		//参数校验
		if ($this->post()->hasError()) {
			$return = Response::gen_error(10001, '', $this->errors);
        	return $this->app->response->setBody($return);
		}

		$queryParams = array();
		if (!empty($this->progress['progress_id'])) {
			$queryParams['progress_id'] = $this->progress['progress_id'];
		}
		if (!empty($this->progress['task_id'])) {
			$queryParams['task_id'] = $this->progress['task_id'];
		}
		if (!empty($this->progress['process_id'])) {
			$queryParams['process_id'] = $this->progress['process_id'];
		}
		if (!empty($this->progress['action_type'])) {
			$queryParams['action_type'] = $this->progress['action_type'];
		}
		if (!empty($this->progress['current_user_id'])) {
			$queryParams['current_user_id'] = $this->progress['current_user_id'];
		}
		if (!empty($this->progress['status'])) {
			$queryParams['status'] = $this->progress['status'];
		}
		if (!empty($this->progress['progress_content'])) {
			$queryParams['progress_content'] = $this->progress['progress_content'];
		}

		$result = Progress::getInstance()->getDataList($queryParams);

		if ($result === FALSE) {
        	$return = Response::gen_error(50001);
        }elseif (empty($result) && !is_array($result)) {
        	$return = Response::gen_error(50002);
        }else{
        	$return = Response::gen_success(Format::outputData($result, $this->sample, TRUE));
        }

        $this->app->response->setBody($return);
	}

	/**
     * 参数初始化
     */
	private function _init() {

		$this->rules = array(
			'progress_id'      => array(
				'type' => 'integer',
			),
			'task_id'          => array(
				'type' => 'integer',
			),
			'process_id'       => array(
				'type' => 'integer',
			),
			'action_type'      => array(
				'type' => 'integer',
			),
			'current_user_id'  => array(
				'type' => 'integer',
			),
			'status'           => array(
				'type' => 'integer',
			),
			'progress_content' => array(
				'type' => 'string',
			),
		);

		$this->progress = $this->post()->safe();
		$this->errors = $this->post()->getErrors();
	}
}