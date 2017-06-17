<?php
namespace WorkFlowAtom\Modules\Process;

use Libs\Util\Format;
use \WorkFlowAtom\Package\Process\TaskType;
use \WorkFlowAtom\Package\Common\Response;

class TaskTypeEdit extends \WorkFlowAtom\Modules\Common\BaseModule{
	
	private $params = array();
	private $sample;

	public function run() {
		$this->_init();
		
		$this->sample = TaskType::getInstance()->getFields();
		
		//参数校验
        if($this->post()->hasError()){
        	$return = Response::gen_error(10001, '', $this->post()->getErrors());
        	return $this->app->response->setBody($return);
        }
		
		$typeInfo = TaskType::getInstance()->getDataList(array(
			'type_name'			=> $this->params['type_name'],
			'type_parent_id'	=> $this->params['type_parent_id']
		));
		
		if(empty($this->params['type_id'])) {
			unset($this->params['type_id']);
			
			if(!empty($typeInfo)) {
				$return = Response::gen_error(50012, '插入检测，类型名重复');
				return $this->app->response->setBody($return);
			}
			
			$result = TaskType::getInstance()->insert($this->params);
		}else {
			unset($typeInfo[$this->params['type_id']]);
			
			if(!empty($typeInfo)) {
				$return = Response::gen_error(50012, '更新检测，类型名重复' . var_export($typeInfo, true));
				return $this->app->response->setBody($return);
			}
			
			$result = TaskType::getInstance()->update($this->params);
		}

        if ($result === FALSE) {
        	$return = Response::gen_error(50001);
        }elseif (empty($result)) {
        	$return = Response::gen_error(50012);
        }else{
			if(empty($this->params['type_id'])) {
				$this->params['type_id'] = $result;
			}
            
        	$return = Response::gen_success(Format::outputData($this->params, $this->sample));
        }

    	$this->app->response->setBody($return);
	}
	
	protected function _init() {
		
		$this->rules = array(
			'type_id'	=> array(
                'required'	=> true,
                'type'		=> 'integer',
                'default'	=> 0,
            ),
            'type_name'	=> array(
                'required'		=> true,
                'type'			=> 'string',
                'maxLength'		=> 30,
				'allowEmpty'	=> false
            ),
            'status'	=> array(
                'type'		=> 'integer',
                'default'	=> 1,
            ),
			'type_parent_id'	=> array(
                'type'		=> 'integer',
                'default'	=> 0,
            ),
        );
		
		$this->params = $this->post()->safe();
	}
}