<?php
namespace Atom\Modules\Core;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Core\UserLogin;

/**
 * 修改登录信息
 * @author minggeng@meilishuo.com
 * @since 2015-06-4
 */

class UserLoginUpdate extends \Atom\Modules\Common\BaseModule {

    protected $data = array();

	public function run() {

        $this->_init();

        $result = UserLogin::model()->deleteLogicalByUserId($this->data);

        if ($result === FALSE) {
        	$return = Response::gen_error(50001);
        }elseif (empty($result)) {
        	$return = Response::gen_error(50012);
        }else{
        	$return = $result;
        }

    	$this->app->response->setBody($return);
	}	

    /**
     * 参数初始化
     */
    protected function _init(){
        $this->rules = array(
			'user_id'		=> array(
                'required'	=> true,
                'type'		=> 'integer',
                'maxLength'	=> 11,
			),
			'session'		=> array(
                'type'		=> 'string',
                'maxLength'	=> 32,
			),
        );
		$this->data     = $this->post()->safe();
    }

}
