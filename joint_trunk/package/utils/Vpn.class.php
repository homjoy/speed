<?php
namespace Joint\Package\Utils;
use Joint\Package\Common\BasePackage;
/**
 * vpn 登陆陆权限
 * @author wulianglong&hongzhou@meilishuo.com
 *  @date 2015-8-11
 */
defined('VPN_WIFI_HOST') || define("VPN_WIFI_HOST", 'http://yz.it.api01.meiliworks.com/');
class Vpn {
	
	private static $sharedkey = 'Pmlsit2015vpn'; //共享密钥 
	
	
	/**
	 * 设置用户
	 * @param string $op 操作者
	 * @param string $u 被操作者
	 * @param string $p 密码
	 * @param number $enabled 1可用状态，0禁用状态
	 * @return boolean
	 */
	public static function setUser($op = '', $u = '', $p = '', $enabled = 1) {
		$op = trim($op);
		$u = trim($u);
		$p = trim($p);
		if(empty($op) || empty($u) || empty($p) || !in_array($enabled, array(0, 1))) {
			return FALSE;
		}
		
		$seckey = self::getSeckey($op);

		$get = array(
			'act'     => 'setuser',
			'seckey'  => $seckey,
			'op'      => $op,
			'u'       => $u,
			'enabled' => $enabled,
		);
		$post = array('p' => $p);

		$url = VPN_WIFI_HOST. 'apis/vpn-users.php?' . http_build_query($get);
		$curl_obj = new  \Libs\Sphinx\curl;
		$ret = $curl_obj->post($url, http_build_query($post));
		if(!isset($ret['body']) || empty($ret['body'])) {
			return FALSE;
		}
		$body = $ret['body'];
		$body = json_decode($ret['body'], TRUE);
		if(json_last_error() != JSON_ERROR_NONE) {
			return FALSE;
		}
		
		//------ 临时记录
		//$log_arr = $body;
		//$log_arr['operator'] = $op;
		//$log_arr['new_pwd'] = $p;
		//$msg = json_encode($log_arr);
       // $this->app->log->log('api/package/passport/visitor_wifi.log', $msg);
		
		//------ 记录结束
		
		$return = FALSE;
		if('OK' == $body['__STATUS__']) {
			$return = array('code' => '200', 'message' => '操作成功！');
		}else {
			switch ($body['__MSG__']) {
				case 'WEAKPASS':
					$return = array('code' => '400', 'message' => '密码强度不够');
					break;
				case 'INVALIDCHAR':
					$return = array('code' => '400', 'message' => '存在不被允许的特殊字符');
					break;
				default:
					$return = array('code' => '400', 'message' => '操作失败！');
					break;
			}
		}
				
		return $return;
	}	
	
	/**
	 * 
	 * 获取用户的状态
	 * @param string $op 操作人
	 * @param string $u  被操作人
	 * @return boolean|array
	 */
	public static function getStatus($op = '', $u = '') {
		$op = trim($op);
		$u = trim($u);
		if(empty($op) || empty($u)) {
			return FALSE;
		}
		
		$seckey = self::getSeckey($op);

		$get = array(
			'act'    => 'status',
			'seckey' => $seckey,
			'op'     => $op,	
			'u'      => $u,
		);
		
		$url = VPN_WIFI_HOST. 'apis/vpn-users.php?' . http_build_query($get);
		$curl_obj = new  \Libs\Sphinx\curl;
		$ret = $curl_obj->get($url);
		
		if(!isset($ret['body']) || empty($ret['body'])) {
			return FALSE;
		}
		$body = $ret['body'];
		$body = json_decode($ret['body'], TRUE);
		if(json_last_error() != JSON_ERROR_NONE) {
			return FALSE;
		}
		
		
		if('OK' == $body['__STATUS__']) {
			$return = array('code' => '200', 'message' => 'OK', 'data' => $body['__MSG__']);
		}else {
			switch ($body['__MSG__']) {
				case 'USER NOT FOUND':
					$return = array('code' => '-2', 'message' => '未找到用户！');
					break;
				default:
					$return = array('code' => '-1', 'message' => '查询失败！');
			}
		}
		
		return $return;
	}

	/**
	 * 设置用户的状态
	 * @param string $op 操作人
	 * @param string $u 被操作人
	 * @param number $enabled 状态 1可用状态 ， 0禁用状态
	 * @return boolean|Ambigous <boolean, multitype:string >
	 */
	public static function setStatus($op = '', $u = '', $enabled = 0) {
		$op = trim($op);
		$u = trim($u);
		$enabled = intval($enabled);
		if(empty($op) || empty($u) || !in_array($enabled, array(0, 1))) {
			return FALSE;
		}
		
		$seckey = self::getSeckey($op);
		
		$get = array(
			'act'     => 'setuser',
			'seckey'  => $seckey,
			'op'      => $op,
			'u'       => $u,
			'enabled' => $enabled,
		);
		$url = VPN_WIFI_HOST. 'apis/vpn-users.php?' . http_build_query($get);
		$curl_obj = new  \Libs\Sphinx\curl;
		$ret = $curl_obj->get($url);
		
		if(!isset($ret['body']) || empty($ret['body'])) {
			return FALSE;
		}
		$body = $ret['body'];
		$body = json_decode($ret['body'], TRUE);
		if(json_last_error() != JSON_ERROR_NONE) {
			return FALSE;
		}
		
		$return = FALSE;
		if('OK' == $body['__STATUS__']) {
			$return = array('code' => '200', 'message' => '操作成功！');
		}else {
			$return = array('code' => '200', 'message' => '操作失败,无权限或用户不存在！');
		}
		
		return $return;		
	}
	
	/**
	 * 删除一个用户
	 */
	/**
	 * 删除一个用户
	 * @param string $op 操作人
	 * @param string $u  被操作人
	 * @return boolean|array
	 */
	public static function remove($op = '', $u = '') {
		$op = trim($op);
		$u = trim($u);
		if(empty($op) || empty($u)) {
			return FALSE;
		}
		
		$seckey = self::getSeckey($op);
		
		$get = array(
			'act'    => 'REMOVE',
			'seckey' => $seckey,
			'op'     => $op,
			'u'      => $u,
		);
		
		$url = VPN_WIFI_HOST. 'apis/vpn-users.php?' . http_build_query($get);
		$curl_obj = new  \Libs\Sphinx\curl;
		$ret = $curl_obj->get($url);
		
		if(!isset($ret['body']) || empty($ret['body'])) {
			return FALSE;
		}
		$body = $ret['body'];
		$body = json_decode($ret['body'], TRUE);
		if(json_last_error() != JSON_ERROR_NONE) {
			return FALSE;
		}
		
		if('OK' == $body['__STATUS__']) {
			$return = array('code' => '200', 'message' => 'OK', 'data' => $body['__MSG__']);
		}else {
			switch ($body['__MSG__']) {
				case 'USER NOT FOUND':
					$return = array('code' => '-1', 'message' => '未找到用户！');
					break;
				default:
					$return = array('code' => '-1', 'message' => '操作失败！');
			}
		}

		return $return;		
	}
	
	/**
	 * 计算 seckey
	 * @param string $op  邮箱前缀
	 * @return boolean|string
	 */
	private static function getSeckey($op = '') {
		$op = strtok($op, '@');
		$op = trim($op);
		
		$salt = self::$sharedkey . $op . time();
		$seckey = md5($salt);
		
		return $seckey;
	}
	
	
}