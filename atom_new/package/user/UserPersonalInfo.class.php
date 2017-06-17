<?php
namespace Atom\Package\User;

use Atom\Package\Common\SqlBase;
class UserPersonalInfo extends SqlBase {
	
	private static $instance = NULL ;
	private static $table    = 'user_personal_info';
	private static $pk       = 'id';
	private static $col      = 'id, user_id, birthday, mobile, mobile_another, telephone, qq, coat_size, coat_color, pants_size, shoes_size, others';
	private static $db_name  = SqlBase::DB_STAFF;
	
	/** 所有所有字段及类型 **/
	private static $fields_type = array(
			'id'             => 'int',
			'user_id'        => 'int',    //员工id
			'birthday'       => 'string', //生日
			'mobile'         => 'string', //手机
			'mobile_another' => 'string', //手机
			'telephone'      => 'string', //座机
			'qq'             => 'string', //QQ号
			'coat_size'      => 'string', //上衣尺码
			'coat_color'     => 'string', //上衣颜色
			'pants_size'     => 'string', //裤码
			'shoes_size'     => 'string', //鞋码
			'others'         => 'string', //其他信息 json格式存入
	);	
	/** 允许修改的字段 **/
	private static $update_fields = array(
			'birthday'       => 'string' ,
			'mobile'         => 'string' ,
			'mobile_another' => 'string' ,
			'telephone'      => 'string' ,
			'qq'             => 'string' ,
			'coat_size'      => 'string' ,
			'coat_color'     => 'string' ,
			'pants_size'     => 'string' ,
			'shoes_size'     => 'string' ,
			'others'         => 'string' ,
	);	
	
	private function __construct() {}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	 * 获取给定日期内过生日的用户
	 * @param unknown $days
	 * @return boolean|array
	 */
	public function getBirthday($days = array()) {
		if(empty($days) || !is_array($days)) {
			return FALSE;
		}
		
		foreach ($days as $key => $val) {
			$year = date('Y', time());
			$val = $year . '-' . $val; //传过来的只是 m-d
			$val = date('m-d', strtotime($val));
			$days[$key] = $val;
		}

		$days = array_unique($days);
		$days = "'" . implode("','", $days) . "'";
		
		$sql = 'select ' . self::$col . ' from ' . self::$table . " where date_format(birthday, '%m-%d') in ({$days})";
		
		return self::getConn(self::$db_name)->read($sql, array(), FALSE, 'user_id');
	}
	
}