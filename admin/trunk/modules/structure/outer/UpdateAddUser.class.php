<?php
namespace Admin\Modules\Structure\Outer;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Account\UserOutsourcingInfo;


/**
 * 用户信息
 * @author hongzhou@meilishuo.com
 * @since 2015-8-19 下午12:53:13
 */

class UpdateAddUser extends BaseModule {

	private   $params = NULL;

	public function run() {

        $this->_init();

        $user_info = UserOutsourcingInfo::getInstance()->searchUserOutsourcingInfo($this->params);
        if(empty($user_info)){
            return $this->app->response->setBody(Response::gen_success(array()));
        }else{
            $user_info =is_array($user_info)?array_pop($user_info):'';
            return $this->app->response->setBody(Response::gen_success($user_info));
        }
	}
	private function _init() {

		$this->rules = array(
			'out_user_id' => array(
				'type'=>'integer',
			),
            'status' => array(
                'type'=>'multiId',
                'default'=> array(1,2,3)
            )
		);
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
	}
	
}