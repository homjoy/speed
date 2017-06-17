<?php
namespace Admin\Modules\Auth;

use Admin\Package\User\UserInfo;
use Admin\Package\User\UserRole;
use Admin\Package\User\UserMotor;
use Admin\Package\Base\Utils;
// use Admin\Package\Oauth\OauthLogin;

class OauthLogin extends \Admin\Modules\Common\BaseModule {
	
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
			$this->view = array('code' => 400, 'message' => 'authorize code 读取失败！');
			return FALSE;
		}
		
		$params= http_build_query(array(
			'client_id'		=> OPEN_APPKEY,
			'client_secret'	=> OPEN_APPSECRET,
			'grant_type'	=> 'authorization_code',
			'redirect_uri'	=> 'http://' . SERVER_NAME . '/auth/login/oauth_login',
			'code'			=> $code
		));
        $url = OPEN_API . 'oauth/access_token';
        $curl_obj = new \Phplib\Sphinx\curl ;
        $res = $curl_obj->post($url,$params);
		
		if(isset($res['body'])){
            $this->access_token_info = json_decode($res['body'],true);
			if(empty($this->access_token_info)) {
				$this->view = array('code' => 400, 'message' => 'access_token 解析失败！');
				return FALSE;
			}elseif(200 != $this->access_token_info['code']) {
				$this->view = $this->access_token_info;
				return FALSE;
			}
			
			$params= http_build_query(array(
				'client_id'		=> OPEN_APPKEY,
				'access_token'	=> $this->access_token_info['access_token']
			));
			$url = OPEN_API . 'oauth/statuses';
			$curl_obj = new \Phplib\Sphinx\curl ;
			$res = $curl_obj->post($url,$params);
			
			if(isset($res['body'])){
				$this->user_info = json_decode($res['body'],true);
				if(empty($this->user_info)) {
					$this->view = array('code' => 400, 'message' => '用户状态信息解析失败！');
					return FALSE;
				}elseif(200 != $this->user_info['code']) {
					$this->view = $this->user_info;
					return FALSE;
				}
			}else {
				$this->view = array('code' => 400, 'message' => '用户状态信息获取失败！');
				return FALSE;
			}
		}else {
			$this->view = array('code' => 400, 'message' => 'access_token 获取失败！');
			return FALSE;
		}

        return TRUE;
    }
	
	private function load() {
		//获取本地用户信息
		$queryParmam = array('user_mail' => $this->user_info['data']['mail'], 'status' => 1);
		$user = Utils::getUserInfo($queryParmam);
		
		if(is_object($user) && $user->user_id){
			\Admin\Modules\Common\BaseSession::Singleton()->marked($user, $this->user_info['expires_in']);
			$this->view = array('code' => 200, 'message' => '登录成功！', 'url' => '/dashboard');
			return TRUE;
		}else{
			$param = array(
				'user_mail' 		=> $this->user_info['data']['mail'],
				'user_name' 		=> $this->user_info['data']['name'],
				'user_role_id' 		=> 1,
				'speed_id'			=> $this->user_info['data']['id'],
				'user_avatar'		=> $this->user_info['data']['avatar_small'],
				'user_depart_id'	=> Utils::getLocationDepartId($this->user_info['data']['departid'])
			);
				
			$addResult = UserInfo::getInstance()->addData($param);
			if ($addResult) {
				$queryParmam = array('user_mail' => $this->user_info['data']['mail'], 'status' => 1);
				$user = Utils::getUserInfo($queryParmam);
				\Admin\Modules\Common\BaseSession::Singleton()->marked($user, $this->user_info['expires_in']);
				
				$this->view = array('code' => 200, 'message' => '登录成功！请确认部门信息是否正确！', 'url' => '/user/profile#tab_2-2');
				
				return TRUE;
			} else{
				
				$this->view = array('code' => 400, 'message' => '您没有speed系统账号，请联系管理员申请！');
				return FALSE;
			}
		}
		
		return TRUE;
	}

}

