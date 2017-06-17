<?php
namespace Apicloud\Modules\Auth;

use Libs\Cache\Memcache;
use Apicloud\Package\Common\Response;
use Libs\Serviceclient\Client;

/**
 * 登出
 * Class Logout
 * @package Apicloud\Modules\Auth
 * User:  guojiezhu
 * Date:  2016-04-05
 */
class Logout extends \Frame\Module {

    private $speed_token = NULL;

    public function run() {

        if(!$this->_init()) {
            $this->app->response->setBody(Response::gen_error(10002));
            return FALSE;
        }

        Memcache::instance()->delete($this->speed_token);	//清除
        $mark = Memcache::instance()->get($this->speed_token); //确认是否已清除

        //修改user_login登陆状态
        $clientObj = new Client();
        $clientObj->call('atom', '/core/user_login_update', array('user_id' => $this->app->currentUser['user_id']));

        if(!empty($mark)) {
            $this->app->response->setBody(Response::gen_error(10001, '操作失败，请重新退出！'));
            return FALSE;
        }

        $this->app->response->setBody(Response::gen_success(array('message' => '已正常退出！')));
    }

    private function _init() {
        $this->speed_token = isset($this->request->GET['speed_token']) ? trim($this->request->GET['speed_token']) : '';
        if(empty($this->speed_token)) {
            return FALSE;
        }
        return TRUE;
    }

}