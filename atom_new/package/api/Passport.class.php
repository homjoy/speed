<?php
namespace Atom\Package\Api;

/**
 * 通行证 登陆验证 修改密码
 * @author hepang@meilishuo.com
 * @since 2015-08-21
 */

class Passport{

    private static $instance = null;

    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
		//defined('PASSPORT_HOST') || define("PASSPORT_HOST", 'http://172.16.0.123');
		defined('PASSPORT_HOST') || define("PASSPORT_HOST", 'http://yz.it.api01.meiliworks.com');
    }

	const SUCCESS 			= 200;
	const UNKNOWN 			= 10000;

	const PARAM_EMPTY		= 10001;
	const PARAM_INVALID		= 10002;
	const PARAM_USE_SPACE		= 10003;
	const PARAM_SHORT_LENGTH	= 10004;
	const PARAM_NO_INTEGER		= 10005;
	const PARAM_UPPER_LOWER		= 10006;
	const PARAM_SPECIAL_SIGN	= 10007;
	const PARAM_NOT_ALLOWED		= 10008;
	const PARAM_SAME_PASSWORD		= 10009;
	const PARAM_INCLUDE_PASSWORD	= 10010;
	const PARAM_SAME_TO_MAIL		= 10011;
	const PARAM_INCLUDE_MAIL		= 10012;
	const PARAM_INCLUDE_SPECIAL		= 10013;

	const AUTH_ERR 			= 20000;
	const AUTH_SALT_ERR 	= 20001;
	const AUTH_CIPHER_ERR	= 20002;
	const AUTH_EXPIRED		= 20003;
	const AUTH_PASSWORD_ERR	= 20004;
	const AUTH_INVALID_ACCOUNT	= 20005;
	const AUTH_OLD_PASSWORD_ERR	= 20006;
	const AUTH_WEAK_PASSWORD	= 20007;
	const AUTH_INVALID_CHAR	= 20008;
	const AUTH_INVALID_MAIL	= 20009;

	public static $ErrMsg = array(
		self::SUCCESS		=> '成功',
		self::UNKNOWN		=> '未知错误',

		self::PARAM_EMPTY	=> '参数不能为空',
		self::PARAM_INVALID	=> '非法参数',
		self::PARAM_USE_SPACE		=> '请勿使用空格',
		self::PARAM_SHORT_LENGTH	=> '长度不能小于8位',
		self::PARAM_NO_INTEGER		=> '必须包含数字',
		self::PARAM_UPPER_LOWER		=> '必须包含大写字母和小写字母',
		self::PARAM_SPECIAL_SIGN	=> '必须包含一个特殊符号[]~!@#$%^*=,._-',
		self::PARAM_NOT_ALLOWED		=> '包含不允许的字符',
		self::PARAM_SAME_PASSWORD	=> '新旧密码不能相同，不区分大小写',
		self::PARAM_INCLUDE_PASSWORD	=> '新密码不能是旧密码的子集，旧密码也不能是新密码的子集，不区分大小写',
		self::PARAM_SAME_TO_MAIL		=> '新密码与用户名不能相同，不区分大小写',
		self::PARAM_INCLUDE_MAIL		=> '密码不能是用户名的子集，用户名也不能是密码的子集，不区分大小写',
		self::PARAM_INCLUDE_SPECIAL		=> '密码不能包含特殊符号&+',

		self::AUTH_ERR		=> '验证失败',
		self::AUTH_SALT_ERR	=> 'SALT错误',
		self::AUTH_CIPHER_ERR	=> '密文错误',
		self::AUTH_EXPIRED		=> '帐户已过期',
		self::AUTH_PASSWORD_ERR	=> '当前密码不正确',
		self::AUTH_INVALID_ACCOUNT	=> '账户已经被冻结或无此账户',
		self::AUTH_OLD_PASSWORD_ERR	=> '旧密码不符',
		self::AUTH_WEAK_PASSWORD	=> '密码强度不够',
		self::AUTH_INVALID_CHAR	=> '存在不被允许的特殊字符',
		self::AUTH_INVALID_MAIL	=> '非法账户名',
	);

    public static function getCurlObj() {
    	static $curl_obj = NULL;
    	if(is_null($curl_obj)) {
    		$curl_obj = new \Libs\Sphinx\curl;
    	}
    	return $curl_obj;
    }

	/*
	 *  错误代码返回结构
	 *  @param $code 错误代码 int
	 *  @retsult array
	 */
	public static function genResponse($code) {

		$code = intval($code);
		$ret = array();

		!isset(self::$ErrMsg[$code]) && $code = 10000;

		$ret['code']	= $code;
		$ret['msg'] 	= self::$ErrMsg[$code];
		return $ret;
	}

	/**
	 * 登录验证
	 * @param array $params
	 */
    public static function checkPassword($mail = '', $password = '') {

    	if(empty($mail) || empty($password)) {
    		return self::genResponse(10001);
    	}elseif (!strpos($mail, '@meilishuo.com')) {
    		return self::genResponse(10002);
    	}

    	$mail = trim($mail);
    	$password = trim($password);

		//获取salt
		$salt = self::_getSalt($mail);
		if(!$salt) {
			return self::genResponse(20001);
		}

		//计算密文		
		$cipher = self::_enCrypt($salt, $password);
		if(!$cipher) {
			return self::genResponse(20002);
		}

		//验证密文
		return self::_checkCipher($cipher, $mail);
    }

	/**
	 * 修改密码
	 * @param string $mail
	 * @param string $old_pwd
	 * @param string $new_pwd
	 * @return  false请求失败，code=200修改成功，其它情况均为失败，原因参见message
	 */
	public static function editPassword($mail = '', $oldPassword = '', $newPassword = '') {

		$mail = trim($mail);

		//强度验证
		$check = self::complexMatch($mail, $oldPassword, $newPassword);
		if($check['code'] != 200) {
			return $check;
		}

		//权限验证
		$check = self::checkPassword($mail, $oldPassword);
		if($check['code'] != 200) {
			return $check;
		}

		//修改密码
		$param = array(
			'pn' => $newPassword,
			'po' => $oldPassword,
		);
		$param = http_build_query($param);
		$url = PASSPORT_HOST . '/apis/mail/users.php?' . http_build_query(array('act' => 'SetPass', 'u' => $mail));
		$ret = self::getCurlObj()->post($url, $param);

		if(!isset($ret['body']) || empty($ret['body'])) {
			return FALSE;
		}

		$body = json_decode($ret['body'], TRUE);
		if(json_last_error() != JSON_ERROR_NONE) {
			return FALSE;
		}

		if('OK' == $body['__STATUS__']) {
			return self::genResponse(200);
		}else{
			switch ($body['__MSG__']) {
				case 'ABSENCE':
					return self::genResponse(20005);
					break;
				case 'MISMATCH':
					return self::genResponse(20006);
					break;
				case 'WEAKPASS':
					return self::genResponse(20007);
					break;
				case 'INVALIDCHAR':
					return self::genResponse(20008);
					break;
				default:
					return self::genResponse(20009);
					break;
			}
		}
	}

	/** 
	 * 密码复杂性校验
	 * code=200表示密码可用，其它情况均为不可用
	 **/
	public static function complexMatch($mail = '', $oldPassword = '', $newPassword = '') {

		//检测参数合法
    	if(empty($mail) || empty($newPassword) || empty($oldPassword)) {
    		return self::genResponse(10001);
    	}

    	//检测新密码合法
		if(trim($newPassword) !== $newPassword) {
			return self::genResponse(10003);
		}

		if(strlen($newPassword) < 8) {
			return self::genResponse(10004);
		}
		
		if(!preg_match('/[0-9]/', $newPassword)) {
			return self::genResponse(10005);
		}
		
		if(!preg_match('/[a-z]/', $newPassword) || !preg_match('/[A-Z]/', $newPassword)) {
			return self::genResponse(10006);
		}

		if(preg_match('/[\+\&]/', $newPassword)) {
			return self::genResponse(10013);
		}

		//if(!preg_match('/[\[\]\(\)\~\!\@\#\$\%\^\*\=\,\.\_\-]/', $newPassword)) {
		if(!preg_match('/[\[\]\~\!\@\#\$\%\^\*\=\,\.\_\-]/', $newPassword)) {
			return self::genResponse(10007);
		}
		
		if(preg_match('/[^0-9a-zA-Z\[\]\~\!\@\#\$\%\^\*\=\,\.\_\-]/', $newPassword, $match)) {
			$match = array_pop($match);
			if(trim($match) !== $match) {
				$match = '空格';
			}
			$return = self::genResponse(10008);
			$return['msg'] .= $match;
			return $return;
		}

		//检测新旧密码关联性
		$lowerNewPassword = strtolower($newPassword);
		$lowerOldPassword = strtolower($oldPassword);
		if(strcasecmp($lowerNewPassword, $lowerOldPassword) === 0) {
			return self::genResponse(10009);
		}

		if(preg_match('/(' . preg_quote($lowerOldPassword) . ')+/', $lowerNewPassword) || preg_match('/(' . preg_quote($lowerNewPassword) . ')+/', $lowerOldPassword)) {
			return self::genResponse(10010);
		}

		//检测密码邮箱关联性
		$lowerMail = strtolower($mail);
		if(strcasecmp($lowerMail, $lowerNewPassword) === 0) {
			return self::genResponse(10011);
		}
		
		if(preg_match('/(' . preg_quote($lowerNewPassword) . ')+/', $lowerMail) || preg_match('/(' . preg_quote($lowerMail) . ')+/', $lowerNewPassword)) {
			return self::genResponse(10012);
		}

		return self::genResponse(200);
	}

	/**
	 * 验证密码
	 * @param array $params
	 */
	private static function _checkCipher($cipher = '', $mail = '') {

		if(empty($cipher) || empty($mail)) {
			return FALSE;
		}

		$param = array(
			'act' => 'ChkPass',
			'u'   => $mail,
			'p'   => $cipher,
		);

		$url = PASSPORT_HOST . '/apis/mail/users.php?' . http_build_query($param);
		$ret = self::getCurlObj()->get($url);

		if(!isset($ret['body']) || empty($ret['body'])) {
			return FALSE;
		}

		$body = json_decode($ret['body'], TRUE);
		if(json_last_error() != JSON_ERROR_NONE) {
			return FALSE;
		}

		switch ($body['__STATUS__']) {
			case 'OK':
				$expire = isset($body['__EXPIRE__']) ? $body['__EXPIRE__'] : 0;
				$return = self::genResponse(200);
				$return['expire'] = $expire;
				break;
/*
			case 'EXPIRED':
				$expire = isset($body['__EXPIRE__']) ? $body['__EXPIRE__'] : 0;
				$return = self::genResponse(20003);
				$return['expire'] = $expire;
				break;
*/
			case 'ERROR':
				if (trim($body['__MSG__']) === 'NOT MATCH') {
					$return = self::genResponse(20004);
				}else{
					$return = self::genResponse(10000);
				}
				break;
			default:
				//$return = self::genResponse(20000);
				$expire = isset($body['__EXPIRE__']) ? $body['__EXPIRE__'] : 0;
				$return = self::genResponse(200);
				$return['expire'] = $expire;
				break; 
		}

		return $return ;
	}

	/**
	 * 计算密文
	 * @param array $params
	 */
	private static function _enCrypt($salt = '', $password = '') {

		if(empty($salt) || empty($password)) {
			return FALSE;
		}

		return crypt($password, $salt['salt']); //password为明文密码
	}

	/**
	 * 获取salt
	 * @param array $params
	 */
	private static function _getSalt($mail = '') {

		if(empty($mail) || !strpos($mail, '@meilishuo.com')) {
			return FALSE;
		}

		//参数
		$params = array(
			'act' => 'GetSalt',
			'u'   => $mail,
		);

		$url = PASSPORT_HOST . '/apis/mail/users.php?' . http_build_query($params);
		$ret = self::getCurlObj()->get($url);

		//返回
		if(!isset($ret['body']) || empty($ret['body'])) {
			return FALSE;
		}

		$body = json_decode($ret['body'], TRUE);
		if(json_last_error() != JSON_ERROR_NONE) {
			return FALSE;
		}

		if('OK' != $body['__STATUS__'] || empty($body['salt'])) {
			return FALSE;
		}

		return $body;
	}
	
}
