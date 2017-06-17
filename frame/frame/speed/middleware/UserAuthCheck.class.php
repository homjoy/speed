<?php

namespace Frame\Speed\Middleware;

use Frame\Speed\Exception\AuthException;

/**
 * 认证检查
 * Class AuthCheck
 * @package Frame\Speed\Middleware
 */
class UserAuthCheck extends \Frame\Middleware
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

            //检测speed header
            if (isset($this->app->request->headers['Speed']) && !empty($this->app->request->headers['Speed'])) {

                $userData = explode(';', $this->app->request->headers['Speed']);
                $userInfo = array();
                foreach ($userData as $data) {
                    $param = explode(':', $data);
                    if (isset($param[0]) && isset($param[1])) {
                        $userInfo[$param[0]] = $param[1];
                    }
                }
            }

            if (!empty($userInfo) && isset($userInfo['user_id']) && !empty($userInfo['user_id'])) {
                $this->app->currentUser = $userInfo;
                $this->next->call();
                return;
            }else{
                throw new AuthException('登陆信息已失效，请重新登陆!');
            }

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
