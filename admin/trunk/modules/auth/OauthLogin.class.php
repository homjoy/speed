<?php
namespace Admin\Modules\Auth;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;

class OauthLogin extends BaseModule {
	
	protected $access_token_info;
	protected $user_info;

    public function run() {
        if (!$this->init()) {
            return FALSE;
        }
		
		$this->load();
    }

    private function init() {
		
		$code = isset($this->request->GET['code']) ? $this->request->GET['code'] : NULL;
		if(empty($code)) {
			
			$return = Response::gen_error(10001, '', 'authorize code 读取失败！');
        	return $this->app->response->setBody($return);
			
		}
		
		$params= http_build_query(array(
			'client_id'		=> OPEN_APPKEY,
			'client_secret'	=> OPEN_APPSECRET,
			'grant_type'	=> 'authorization_code',
			'redirect_uri'	=> 'http://' . SERVER_NAME . '/auth/oauth_login',
			'code'			=> $code
		));
        $url = OPEN_API . 'oauth/access_token';
        $curl_obj = $this->getCurlObj() ;
        $res = $curl_obj->post($url,$params);
		
		if(isset($res['body'])){
            $this->access_token_info = json_decode($res['body'],true);
			if(empty($this->access_token_info)) {
				$return = Response::gen_error(10004, '', 'access_token 解析失败！');
				return $this->app->response->setBody($return);
			}elseif(200 != $this->access_token_info['code']) {
				$return = Response::gen_error(20001, '', $this->access_token_info['message']);
				return $this->app->response->setBody($return);
			}
			
			$params= http_build_query(array(
				'client_id'		=> OPEN_APPKEY,
				'access_token'	=> $this->access_token_info['access_token']
			));
			$url = OPEN_API . 'oauth/statuses';
			$res = $curl_obj->post($url,$params);
			
			if(isset($res['body'])){
				$this->user_info = json_decode($res['body'],true);
				if(empty($this->user_info)) {
					$return = Response::gen_error(10004, '', '用户状态信息解析失败！');
					return $this->app->response->setBody($return);
				}elseif(200 != $this->user_info['code']) {
					$return = Response::gen_error(20001, '', $this->user_info['message']);
					return $this->app->response->setBody($return);
				}
			}else {
				$return = Response::gen_error(30001, '', '用户状态信息获取失败！');
				return $this->app->response->setBody($return);
			}
		}else {
			$return = Response::gen_error(30001, '', 'access_token 获取失败！');
			return $this->app->response->setBody($return);
		}

        return TRUE;
    }
	
	private function load() { 
		
		\Admin\Modules\Common\BaseSession::Singleton()->marked($this->user_info, $this->user_info['expires_in']);
		
		header('Location: /');
        exit();
	}
	
	protected function checkSession(){}

}
