<?php 
namespace WorkFlowApi\Modules\Task;

/**
 * 批量导入工作流,用于旧的工作流数据批量导入
 * @author jingjingzhang@meilishuo.com
 * @since 2015-11-16
 */

use WorkFlowApi\Modules\Common\BaseModule;
use WorkFlowApi\Package\Common\Response;

class DataSync extends BaseModule {
	
	private $params;

	public function run() {

		if (!$this->_init()) {
			return FALSE;
		}

		$result = self::getClient()->call('workflowatom', 'process/data_sync', $this->params);
		$result = self::parseApiData($result);

		if ($result === FALSE) {
			$return = Response::gen_error(50001);
		} else if (empty($result)) {
			$return = Response::gen_error(50003);
		} else {
			$return = Response::gen_success($result);
		}

		$this->app->response->setBody($return);
	}

	private function _init() {
		$this->params['data'] = isset($this->request->POST['data']) ? $this->request->POST['data'] : array();

		if (empty($this->params['data'])) {
			$return = Response::gen_error(10001, '参数为空');
        	$this->app->response->setBody($return);
			
			return FALSE;
		}

		return TRUE;
	}
}	