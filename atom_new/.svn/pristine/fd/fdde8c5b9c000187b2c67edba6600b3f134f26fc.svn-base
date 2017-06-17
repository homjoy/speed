<?php
namespace Atom\Package\User;

use Atom\Package\Common\SqlBase;

class UserJobRole extends SqlBase {
	
	private static $instance = NULL ;
	private static $table    = 'user_job_role';
	private static $pk       = 'role_id';
	private static $col      = 'role_id, role_name, role_info, role_level, memo, update_time, status';
	private static $db_name  = SqlBase::DB_STAFF;
	
	/**
	  * 所有所有字段及类型
	 */
	 private static $fields_type = array(
	 		'role_id'     => 'int',
	 		'role_name'   => 'string', //岗位名称
	 		'role_info'   => 'string', //岗位信息
	 		'role_level'  => 'int',    //级别
	 		'memo'        => 'string', //备注
	 		'update_time' => 'string',
	 		'status'      => 'int',    //状态:0无效1有效
	 );
	 	
	 /**
	 * 允许修改的字段，排除主键的其它字段
	 */
	 private static $update_fields = array(
	 		'role_name'   => 'string' ,
	 		'role_info'   => 'string' ,
	 		'role_level'  => 'int' ,
	 		'memo'        => 'string' ,
	 		'update_time' => 'string' ,
	 		'status'      => 'int' ,
	 );
	 
	 public static function getInstance() {
	 	if(empty(self::$instance)) {
	 		self::$instance = new self();
	 	}
	 	return self::$instance;
	 }
	
}