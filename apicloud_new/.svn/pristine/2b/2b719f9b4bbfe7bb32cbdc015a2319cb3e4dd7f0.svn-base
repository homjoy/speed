<?php
namespace Apicloud\Package\Passport;
use Apicloud\Package\Common\BasePackage;
use Illuminate\Support\Facades\Mail;

/*
 * 登陆验证  修改密码
 * @author wulianglong@meilishuo.com
 * @date 2015-2-11, 下午12:23:52
 * edit by guojiezhu@meilishuo.com
 */
class Passport extends BasePackage{
	
	//private static $host = 'http://172.16.0.123'; //邮箱host
	//private static $host = 'http://yz.it.api01.meiliworks.com'; //邮箱host

    /**
     * 权限验证
     * @param string $mail
     * @param string $pwd
     * @return array 仅当code=200时，验证成功，错误消息参见message
     */
    public static function checkPassportNew($mail = '', $pwd = '') {
        $login = array(
            'email'=>$mail,
            'password'=>$pwd,
        );
        $ret = BasePackage::getClient()->call('atom','passport/login',$login);

        return $ret;
    }

	/**
	 * 权限验证
	 * @param string $mail
	 * @param string $pwd
	 * @return array 仅当code=200时，验证成功，错误消息参见message
	 */
	public static function checkPassport($mail = '', $pwd = '') {
		if(empty($mail) || empty($pwd) || !strpos($mail, '@meilishuo.com')) {
			return array('code' => '-1', 'message' => '参数不完整');
		}

		$mail = trim($mail);
		//获取salt
		$salt = self::getSalt($mail);
		if(!$salt) {
			return array('code' => '-1', 'message' => '用户不存在');
		}
		//计算密文		
		$cp = self::doCrypt($salt, $pwd);
		if(!$cp) {
			return array('code' => '-1', 'message' => '参数不完整');
		}
		//验证密文
		$chk = self::chkPass($cp, $mail);
		if($chk['code'] != '200') {
			return $chk ? $chk : array('code' => '-1', 'message' => '验证失败');
		}

		return $chk;
	}
	
	/**
	 * 修改密码
	 * @param string $mail
	 * @param string $old_pwd
	 * @param string $new_pwd
	 * @return  false请求失败，code=200修改成功，其它情况均为失败，原因参见message
	 */
	public static function editPwd($mail = '', $old_pwd = '', $new_pwd = '') {
		
		$mail = trim($mail);

		//权限验证
		$check = self::checkPassport($mail, $old_pwd);
		if($check['code'] != '200') {
			return $check;
		}
		
		//修改密码
		$param = array(
			'pn' => $new_pwd,
			'po' => $old_pwd,
		);
		$param = http_build_query($param);
		$url = MAIL_URL . 'users.php?' . http_build_query(array('act' => 'SetPass', 'u' => $mail));
		$ret = self::getCurlObj()->post($url, $param);
		
		if(!isset($ret['body']) || empty($ret['body'])) {
			return FALSE;
		}
		$body = json_decode($ret['body'], TRUE);
		if(json_last_error() != JSON_ERROR_NONE) {
			return FALSE;
		}
		
		$return = NULL;
		if('OK' == $body['__STATUS__']) {
			$return = array('code' => '200', 'message' => '修改成功');
		}else{
			switch ($body['__MSG__']) {
				case 'ABSENCE':
					$return = array('code' => '-1', 'message' => '账户已经被冻结或无此账户');
					break;
				case 'MISMATCH':
					$return = array('code' => '-1', 'message' => '旧密码不符');
					break;
				case 'WEAKPASS':
					$return = array('code' => '-1', 'message' => '密码强度不够');
					break;
				case 'INVALIDCHAR':
					$return = array('code' => '-1', 'message' => '存在不被允许的特殊字符');
					break;
				default:
					$return = array('code' => '-1', 'message' => '非法账户名');
					break;
			}
		}
		
		return $return;
	}
	
	/** 
	 * 密码复杂性校验
	 * code=200表示密码可用，其它情况均为不可用
	 **/
	public static function complexMatch($new_pwd = '', $old_pwd = '', $uname = '') {

		if(trim($new_pwd) !== $new_pwd) {
			return array('code' => '-1', 'message' => '请勿使用空格');
		}
		
		if(strlen($new_pwd) < 8) {
			return array('code' => '-1', 'message' => '不能小于8位');
		}
		
		if(!preg_match('/[0-9]/', $new_pwd)) {
			return array('code' => '-1', 'message' => '必须包含数字');
		}
		
		if(!preg_match('/[a-z]/', $new_pwd) || !preg_match('/[A-Z]/', $new_pwd)) {
			return array('code' => '-1', 'message' => '必须包含大写字母和小写字母');
		}
		
		//if(!preg_match('/[\[\]\(\)\~\!\@\#\$\%\^\*\+\=\,\.\_\-]/', $new_pwd)) {
		if(!preg_match('/[\[\]\~\!\@\#\$\%\^\*\+\=\,\.\_\-]/', $new_pwd)) {
			return array('code' => '-1', 'message' => '必须包含一个特殊符号[]~!@#$%^*+=,._-');
		}
		
		//if(preg_match('/[^0-9a-zA-Z\[\]\(\)\~\!\@\#\$\%\^\*\+\=\,\.\_\-]/', $new_pwd, $match)) {
		if(preg_match('/[^0-9a-zA-Z\[\]\~\!\@\#\$\%\^\*\+\=\,\.\_\-]/', $new_pwd, $match)) {
			$match = array_pop($match);
			if(trim($match) !== $match) {
				$match = '空格';
			}
			return array('code' => '-1', 'message' => $match . ' 是不允许的字符');
		}
		
		$lower_new = strtolower($new_pwd);
		$lower_old = strtolower($old_pwd);
		if($lower_old){

				if(strcasecmp($lower_new, $lower_old) === 0) {
					return array('code' => '-1', 'message' => '新旧密码不能相同,不区分大小写');
				}
				
				if(preg_match('/(' . preg_quote($lower_old) . ')+/', $lower_new) || preg_match('/(' . preg_quote($lower_new) . ')+/', $lower_old)) {
					return array('code' => '-1', 'message' => '新密码不能是旧密码的子集、旧密码也不能是新密码的子集,不区分大小写');
				}
				
				$lower_uname = strtolower($uname);
				if(strcasecmp($lower_uname, $lower_new) === 0) {
					return array('code' => '-1', 'message' => '新密码与用户名不能相同,不区分大小写');
				}
				
				if(preg_match('/(' . preg_quote($lower_new) . ')+/', $lower_uname) || preg_match('/(' . preg_quote($lower_uname) . ')+/', $lower_new)) {
					return array('code' => '-1', 'message' => '密码不能是用户名的子集、用户名也不能是密码的子集,不区分大小写');
				}
		}
		return array('code' => '200', 'message' => 'OK');
	}
	
	private static function chkPass($cp, $mail) {
		if(empty($cp) || empty($mail)) {
			return FALSE;
		}
		
		$param = array(
			'act' => 'ChkPass',
			'u'   => $mail,
			'p'   => $cp,
		);
		$url = MAIL_URL . 'users.php?' . http_build_query($param);
		$ret = self::getCurlObj()->get($url);
		if(!isset($ret['body']) || empty($ret['body'])) {
			return FALSE;
		}
		$body = $ret['body'];
		$body = json_decode($ret['body'], TRUE);
		if(json_last_error() != JSON_ERROR_NONE) {
			return FALSE;
		}
		
		$return = array('code' => '-1', 'message' => '验证失败');			
		switch ($body['__STATUS__']) {
			case 'OK':
				$expire = isset($body['__EXPIRE__']) ? $body['__EXPIRE__'] : 0; //过期时间
				$return = array('code' => '200', 'message' => 'OK', 'expire' => $expire);
				break;
			case 'EXPIRED':
				$expire = isset($body['__EXPIRE__']) ? $body['__EXPIRE__'] : 0;
				$return = array('code' => '200', 'message' => '帐户已过期', 'expired' => $expire);	//验证成功，但已过期		
				break;
			case 'ERROR':
				$msg = ( trim($body['__MSG__']) === 'NOT MATCH' ) ? '当前密码不正确' : $body['__MSG__'];
				$return = array('code' => '-1', 'message' => $msg);			
				break;
			default:
				$return = array('code' => '-1', 'message' => '验证失败');			
				break; 
		}
		
		return $return ;
	}
	
	private static function doCrypt($salt = '', $pwd = '') {
		if(empty($salt) || empty($pwd)) {
			return FALSE;
		}
		$cp = crypt($pwd, $salt['salt']); //pwd为明文密码
		return $cp;
	}
	
	private static function getSalt($mail = '') {
		if(empty($mail) || !strpos($mail, '@meilishuo.com')) {
			return FALSE;
		}
		$param = array(
			'act' => 'GetSalt',
			'u'   => $mail,
		);
		$url = MAIL_URL . 'users.php?' . http_build_query($param);
		$ret = self::getCurlObj()->get($url);
		if(!isset($ret['body']) || empty($ret['body'])) {
			return FALSE;
		}
		$salt = $ret['body'];
		$salt = json_decode($salt, TRUE);
		if(json_last_error() != JSON_ERROR_NONE) {
			return FALSE;
		}
		
		if('OK' != $salt['__STATUS__'] || empty($salt['salt'])) {
			return FALSE;
		}
		
		return $salt;
	}
	
}
