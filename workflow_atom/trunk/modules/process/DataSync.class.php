<?php
namespace WorkFlowAtom\Modules\Process;

/**
 * 批量导入工作流,用于旧的工作流数据批量导入
 * @author jingjingzhang@meilishuo.com
 * @since 2015-11-16
 */

use \WorkFlowAtom\Package\Common\Response;
use \WorkFlowAtom\Package\Process\Progress;
use \WorkFlowAtom\Package\Process\Task;

class DataSync extends \WorkFlowAtom\Modules\Common\BaseModule {

	private $params;

	public function run() {

		if (!$this->_init()) {
			return FALSE;
		}

		$result = array();
		foreach ($this->params as $k => $v) {
			try {
				Task::getInstance()->beginTran();

				$v['task']['task_content'] = htmlspecialchars_decode(htmlspecialchars_decode($v['task']['task_content']));
				$v['task']['update_time'] = date('Y-m-d H:i:s');

				$result_task = Task::getInstance()->insert($v['task']);

				if (empty($result_task)) {
					throw new \Exception('task 信息插入失败');
				}

				foreach ($v['progress'] as $progress_k => $progress_v) {
					$progress_v['task_id'] = $result_task;
					$v['progress'][$progress_k] = $progress_v;	
				}

				$result_progress = Progress::getInstance()->insert($v['progress']);

				if (empty($result_progress)) {
					throw new \Exception('progress 信息插入失败');
				} 

				$result[$k][$v['task']['order_id']] = $result_task;

				Task::getInstance()->commit();
			} catch (\Exception $e) {
				
				Task::getInstance()->rollback();
				$this->app->log->log('workflowapi_data_sync_log', $v);
			}
		}

		$this->app->response->setBody(Response::gen_success($result));
	}

	protected function _init() {
		
		$this->params =  isset($this->request->POST['data']) ? $this->request->POST['data'] : array();
		
		if (empty($this->params)) {
			
			$return = Response::gen_error(10001, '参数为空');
        	$this->app->response->setBody($return);
			
			return FALSE;
		}
		
		return TRUE;
	}
}