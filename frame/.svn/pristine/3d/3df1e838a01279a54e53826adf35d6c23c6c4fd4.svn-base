<?php

namespace Frame\Speed\Middleware;

use Frame\Speed\Exception\AuthException;

/**
 * 认证检查
 * Class AuthCheck
 * @package Frame\Speed\Middleware
 */
class AuthCheck extends \Frame\Middleware
{
    /**
     * @var array
     */
    protected $settings = array();

    public function __construct($settings = array())
    {
        $this->settings['exclude'] = !isset($settings['exclude']) ? array()
            : (is_array($settings['exclude']) ? $settings['exclude'] : array($settings['exclude']));
    }

    /**
     * 认证检查
     */
    public function call()
    {
        try {
            //请求的接口
            $path = $this->app->request->path;
            //如果不需要认证
            if (in_array($path, $this->settings['exclude'])) {
                $this->next->call();
                return;
            }

            //检测登陆
            if (isset($_COOKIE['speed_token']) && !empty($_COOKIE['speed_token'])) {
                $speedToken = trim($_COOKIE['speed_token']);
            }elseif(isset($this->app->request->GET['speed_token']) && !empty($this->app->request->GET['speed_token'])){
                $speedToken = trim($this->app->request->GET['speed_token']);
            }else{
                throw new AuthException('未登录!');
            }

            if (strlen($speedToken) !== 32) {
                throw new AuthException('token不正确.');
            }

            $userInfo = \Libs\Cache\Memcache::instance()->get($speedToken);
            if (empty($userInfo) || !isset($userInfo['user_id']) || empty($userInfo['user_id'])) {
                throw new AuthException('登陆信息已失效，请重新登陆.');
            }

            //保存登陆信息
            $this->app->currentUser = $userInfo;
            //认证通过之后才能继续调用module 之类的代码
            $this->next->call();
        } catch (AuthException $e) {
            $response = array();
            //http code 401 表示未授权
            $response['code'] = 401;
            $response['error_code'] = $e->getCode();
            $response['error_msg'] = $e->getMessage();

            $this->app->response->setBody($response, true);
        }
    }

}
