<?php
namespace WorkFlowAtom\Modules\Process;

use Libs\Util\Format;
use \WorkFlowAtom\Package\Common\Response;
use \WorkFlowAtom\Package\Process\Process;

class ProcessNodeList extends \WorkFlowAtom\Modules\Common\BaseModule {
	
	private $params;
	private $sample;

	public function run() {
		
		if(!$this->_init()) {
			$return = Response::gen_error(10001, '参数为空');
        	return $this->app->response->setBody($return);
		}
		
		$this->sample = Process::getInstance()->getFields();
		
		$queryParams = array();
		if (!empty($this->params['type_id'])) {
			$queryParams['tasktype_id']	= $this->params['type_id'];
		} 
		if (!empty($this->params['role_id'])) {
			$queryParams['role_id']	= $this->params['role_id'];
		} 
		if (!empty($this->params['process_id'])) {
			$queryParams['process_id'] = $this->params['process_id'];
		}
		
		if(empty($queryParams)) {
			$return = Response::gen_error(10001, '参数异常');
        	return $this->app->response->setBody($return);
		}
		
		if('' != $this->params['status']) {
			$queryParams['status'] = $this->params['status'];
		}
		
		$result = Process::getInstance()->getDataList($queryParams);
		
		if ($result === FALSE) {
        	$return = Response::gen_error(50001);
        } else if (empty($result) || !is_array($result)) {
        	$return = Response::gen_error(30001);
        } else {
			$return = Response::gen_success(Format::outputData($result, $this->sample, true));
		}
		
		$this->app->response->setBody($return);
	}
	
	protected function _init() {
		
//		$this->rules = array(
//			'type_id'	=> array(
//                'required'	=> true,
//                'type'		=> 'integer',
//                'default'	=> 0,
//            ),
//			'role_id'	=> array(
//                'required'	=> true,
//                'type'		=> 'integer',
//                'default'	=> 0,
//            )
//        );
//		
//		$this->params = $this->post()->safe();

		$this->params['type_id'] = isset($this->request->POST['type_id']) ? $this->request->POST['type_id'] : array();
		$this->params['role_id'] = isset($this->request->POST['role_id']) ? $this->request->POST['role_id'] : array();
		$this->params['process_id'] = isset($this->request->POST['process_id']) ? $this->request->POST['process_id'] : array();
		$this->params['status'] = isset($this->request->POST['status']) ? $this->request->POST['status'] : '';
		
		if(empty($this->params['type_id']) && empty($this->params['role_id']) && empty($this->params['process_id'])) {
			return FALSE;
		}
		
		return TRUE;
	}

}