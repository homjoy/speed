<?php
namespace Admin\Modules\Auth\Tree;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Libs\Util\Format;

class AjaxTreeAdd extends BaseModule {
	
	private $params = array();
	private $errors = array();
	public static $VIEW_SWITCH_JSON = TRUE;

	public function run() {

		$this->_init();

		//参数校验
		if ($this->post()->hasError()) {
			$return = Response::gen_error(10001, '', $this->errors);
        	return $this->app->response->setBody($return);
		}

		$result = self::getClient()->call('workflowatom', 'tree/tree_add', $this->params);

		$res = array();
		if ($result['httpcode'] == 200 && $result['content']['error_code'] == 0) {
			$res = array(
				'code' => 200,
				'url' => '/auth/tree/tree'
			);
		} else if ($result['httpcode'] == 200 && isset($result['content']['error_code'])) {
			$res = array(
				'code' => 400,
				'error_msg' => $result['content']['error_msg']
			);
		} else {
			$res = array(
				'code' => 400,
				'error_msg' => '操作失败'
			);
		}
		
    	$this->app->response->setBody($res);
	}
	
	protected function _init() {
		
		$this->rules = array(
			'tree_id'	=> array(
                'required'	=> false,
                'type'		=> 'integer',
                'default'	=> 0,
            ),
            'tree_name'	=> array(
                'required'		=> true,
                'type'			=> 'string',
                'maxLength'		=> 255,
				'allowEmpty'	=> false,
            ),
            'tree_url'	=> array(
                'required'		=> true,
                'type'			=> 'string',
                'maxLength'		=> 255,
				'allowEmpty'	=> false,
            ),
            'status'	=> array(
                'type'		=> 'integer',
                'default'	=> 1,
            ),
			'parent_id'	=> array(
                'type'		=> 'integer',
                'default'	=> 0,
            ),
			'display_position'	=> array(
					'type'		=> 'integer',
					'default'	=> 1,
			),
        );
		
		$this->params = $this->post()->safe();
		$this->errors = $this->post()->getErrors();
	}
}