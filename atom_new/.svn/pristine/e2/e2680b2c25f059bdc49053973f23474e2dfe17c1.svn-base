<?php
namespace Atom\Modules\Core;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Core\UserCaptcha;

/**
 * 新建验证码信息
 * @author minggeng@meilishuo.com
 * @since 2015-12-28
 */

class CreateUserCaptcha extends \Atom\Modules\Common\BaseModule {

    protected $data = array();

	public function run() {
        $this->_init();

        if($this->post()->hasError()){
            $return = Response::gen_error(10001, '', $this->post()->getErrors());
            return $this->app->response->setBody($return);
        }

        $result = UserCaptcha::model()->insertOrUpdate($this->data);

        if ($result === FALSE) {
        	$return = Response::gen_error(50001);
        }elseif (empty($result)) {
        	$return = Response::gen_error(50012);
        }else{
            $this->data['id'] = $result;
        	$return = $result;
        }

    	$this->app->response->setBody($return);
	}	

    /**
     * 参数初始化
     */
    protected function _init(){
        $this->rules = array(
			'type'			=> array(
                'required'	=> true,
                'type'		=> 'integer',
			),
			'captcha' 		=> array(
                'required'	=> true,
                'type'		=> 'string',
                'maxLength'	=> 20,
			),
            'user_id' 		=> array(
                'required'	=> true,
                'type'		=> 'integer',
                'maxLength'	=> 11,
            ),
            'expire_time' 	=> array(
                'required'	=> true,
                'type'		=> 'string',
            ),
        );
		$this->data     = $this->post()->safe();
    }

}
