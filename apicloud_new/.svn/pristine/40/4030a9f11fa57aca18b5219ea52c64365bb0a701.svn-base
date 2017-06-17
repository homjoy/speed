<?php
namespace Apicloud\Modules\Auth;

use Libs\Cache\Memcache;
use Apicloud\Package\Common\Response;
use Apicloud\Package\Core\MlsAccount;
use Apicloud\Package\Notify\UserInfoMonitor;
use Apicloud\Modules\Common\BaseModule;

class CheckLogin extends BaseModule {

    protected $params = array();
    private static $max_day = 90;
    public static $TYPE_MAIL_PWD = 5; //邮箱密码修改登记

	public function run() {
        $this->_init();
		
		$mark = Memcache::instance()->get($this->params['speed_token']);

        $is_login = FALSE;
        $is_expire = FALSE;
        if(!empty($mark) && $mark['user_id']) {
			$is_login = TRUE;
		}else {
			$mark = array();
		}

        $mark['monitor'] = UserInfoMonitor::getInstance()->getMonitor($this->currentUser['user_id']);

        $result = array(
            'is_login'              => $is_login,
            'is_expire'             => $is_expire,
            'user'                  => $mark,
            'is_mfa_code_expire'    => FALSE,
        );
		$this->app->response->setBody(Response::gen_success($result));
	}

	private function _init() {
        $this->rules = array(
            'speed_token' => array(
                'required' => true,
                'type' => 'string',
            ),
        );
        $this->params   = $this->query()->safe();
        $this->errors   = $this->query()->getErrors();
	}
	
}