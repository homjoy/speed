<?php
namespace Atom\Modules\Core;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Core\UserCaptcha;

/**
 * 获取验证码信息
 * @author minggeng@meilishuo.com
 * @since 2015-12-28
 */

class GetUserCaptcha extends \Atom\Modules\Common\BaseModule {

	protected $params   = array();
	private $sample;
	
	public function run() {
		$this->_init();
		$this->sample   = UserCaptcha::model()->getFields();

        if($this->query()->hasError()){
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }

        $queryParams = $this->query()->params_filter($this->params);
        if(empty($queryParams)){
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }

        $result = UserCaptcha::model()->getList($queryParams);
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
            'type'			=> array(
                'type'		=> 'string',
            ),
            'captcha' 		=> array(
                'type'		=> 'string',
            ),
            'user_id' 		=> array(
                'type'		=> 'integer',
            ),
            'expire_time' 	=> array(
                'type'		=> 'string',
            ),
		);
		$this->params   = $this->query()->safe();
	}

}
