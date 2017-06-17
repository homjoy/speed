<?php
namespace WorkFlowAtom\Modules\Process;

use Libs\Util\Format;
use \WorkFlowAtom\Package\Common\Response;
use \WorkFlowAtom\Package\Process\Process;

class ProcessNodeEdit extends \WorkFlowAtom\Modules\Common\BaseModule {
	
	private $params;
	private $sample;

	public function run() {
		
		if(!$this->_init()) {
			return;
		}
		
		//参数校验
        if($this->post()->hasError()){
        	$return = Response::gen_error(10001, '', $this->post()->getErrors());
        	return $this->app->response->setBody($return);
        }
		
		$this->sample = Process::getInstance()->getFields();
		
		/*if(empty($this->params['pre_process_ids'])) {
			$this->params['pre_process_ids'] = 0;
		}
		
		if(empty($this->params['next_process_ids'])) {
			$this->params['next_process_ids'] = 0;
		}*/
		
		$result = Process::getInstance()->update($this->params);
		
		if ($result === FALSE) {
        	$return = Response::gen_error(50001);
        }elseif (empty($result)) {
        	$return = Response::gen_error(30001);
        }else {
        	$return = Response::gen_success(Format::outputData($this->params, $this->sample));
		}
		
		$this->app->response->setBody($return);
	}
	
	protected function _init() {
		
		/*$this->rules = array(
			'process_id'	=> array(
                'required'	=> true,
                'type'		=> 'integer'
            ),
            'process_name'	=> array(
                'type'			=> 'string',
                'maxLength'		=> 30
            ),
            'status'	=> array(
                'type'		=> 'integer'
            ),
			'role_id'	=> array(
                'type'		=> 'integer'
            ),
			'pre_process_ids'	=> array(
                'type'			=> 'string',
                'maxLength'		=> 255
            ),
			'next_process_ids'	=> array(
                'type'			=> 'string',
                'maxLength'		=> 255
            ),
        );
		
		$this->params = $this->post()->safe();*/
		
		$this->params = $this->request->POST;
		
		if(empty($this->params['process_id'])) {
			$return = Response::gen_error(10001, '参数错误');
        	$this->app->response->setBody($return);
			
			return FALSE;
		}
		
		return TRUE;
	}

}