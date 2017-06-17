<?php
namespace WorkFlowAtom\Modules\Process;

use Libs\Util\Format;
use \WorkFlowAtom\Package\Common\Response;
use \WorkFlowAtom\Package\Process\Progress;

class ProgressCreate extends \WorkFlowAtom\Modules\Common\BaseModule {
	
	private $params;
	private $sample;

	public function run() {
		
		$this->_init();
		
		//参数校验
        if($this->post()->hasError()){
        	$return = Response::gen_error(10001, '', $this->post()->getErrors());
        	return $this->app->response->setBody($return);
        }
		
		$this->sample = Progress::getInstance()->getFields();
		
		$result = Progress::getInstance()->insert($this->params);
		
		if ($result === FALSE) {
        	$return = Response::gen_error(50001);
        }elseif (empty($result)) {
        	$return = Response::gen_error(50003);
        }else {
			$this->params['progress_id'] = $result;
            
        	$return = Response::gen_success(Format::outputData($this->params, $this->sample));
		}
		
		$this->app->response->setBody($return);
	}
	
	protected function _init() {
		
		$this->rules = array(
			'task_id'	=> array(
                'required'	=> true,
                'type'		=> 'integer',
                'default'	=> 0,
            ),
			'process_id'	=> array(
                'required'	=> true,
                'type'		=> 'integer',
                'default'	=> 0,
            ),
			'action_type'	=> array(
                'type'		=> 'integer',
                'default'	=> 0,
            ),
			'status'	=> array(
				'required'	=> true,
                'type'		=> 'integer',
                'default'	=> 0,
            ),
			'progress_content'	=> array(
                'required'		=> true,
                'type'			=> 'string'
            ),
			'current_user_id'	=> array(
                'required'	=> true,
                'type'		=> 'integer',
                'default'	=> 0,
            )
        );
		
		$this->params = $this->post()->safe();
	}

}