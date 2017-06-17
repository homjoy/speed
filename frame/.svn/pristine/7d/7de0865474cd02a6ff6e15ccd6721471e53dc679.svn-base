<?php

namespace Frame\Speed\Middleware;

use Frame\Speed\Exception\AuthException;

/**
 * 认证检查
 * Class AuthCheck
 * @package Frame\Speed\Middleware
 */
class WorkflowApiAuthCheck extends \Frame\Middleware
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
            if (isset($this->app->request->POST['speed_user_id']) && !empty($this->app->request->POST['speed_user_id'])) {

                $speed_user_id = $this->app->request->POST['speed_user_id'];

                defined('ATOM') || define('ATOM', 'http://atom.speed.meilishuo.com/');
                $curl_obj = new \Libs\Sphinx\curl;
                $res = $curl_obj->get(ATOM . 'account/get_user_info?user_id=' . $speed_user_id . '&status=1,2,3');

                if (isset($res['body'])) {
                    $speedUserInfo = json_decode($res['body'],true);

                    if (empty($speedUserInfo)) {
                        throw new AuthException('用户信息解析失败');
                    } else if (200 != $speedUserInfo['code']) {
                        throw new AuthException($speedUserInfo['message']);
                    } else if (empty($speedUserInfo['data'])) {
                        throw new AuthException('获取用户信息为空');
                    }

                    $speedUserInfo = current($speedUserInfo['data']);
                    $userInfo = array(
                        'id'        => $speedUserInfo['user_id'],
                        'depart_id' => $speedUserInfo['depart_id'],
                        'name'      => $speedUserInfo['name_cn'],
                        'mail'      => $speedUserInfo['mail'] . '@meilishuo.com',
                    );
                } else {
                    throw new AuthException('获取不到用户信息');
                }
				
            } else {
                throw new AuthException('未登录!');
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
