<?php
namespace Atom\Modules\Core;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Core\UserLogin;

/**
 * 获取登录信息列表
 * @author minggeng@meilishuo.com
 * @since 2015-06-04
 */

class UserLoginList extends \Atom\Modules\Common\BaseModule {

	protected $params   = array();
	private $sample;
	
	public function run() {
		$this->_init();
		$this->sample   = UserLogin::model()->getFields();

        $queryParams = $this->query()->params_filter($this->params);
        $queryParams['status'] = $this->params['status'];

        $result = UserLogin::model()->getList($queryParams);
        if ($result === FALSE) {
        	$return = Response::gen_error(50001);
        }elseif (empty($result)) {
        	$return = Response::gen_error(30001);
        }else{
        	$return = Response::gen_success(Format::outputData($result, $this->sample, TRUE));
        }
    	$this->app->response->setBody($return);
	}	

	/**
	 * 参数初始化
	 */
	protected function _init(){
		$this->rules = array(
			'user_id'=>array(
				'type'=>'string',
			),
			'session'=>array(
				'type'=>'string',
			),
            'status'=>array(
                'type'=>'integer',
            ),
		);
		$this->params   = $this->query()->safe();

        if(!empty($this->params['user_id'])){
            $this->params['user_id'] = explode(',',$this->params['user_id']);
        }
        if(!empty($this->params['session'])) {
            $this->params['session'] = explode(',', $this->params['session']);
        }
	}

}
