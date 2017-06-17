<?php
namespace Atom\Package\User;

use Atom\Package\Common\SqlBase;
class UserJobLevel extends SqlBase {
	
	private static $instance = NULL ;
	private static $table    = 'user_job_level';
	private static $pk       = 'level_id';
	private static $col      = 'level_id, level_name, level_info, memo, update_time, status';
	private static $db_name  = SqlBase::DB_STAFF;
	 /**
	  * 所有所有字段及类型
	 */
	 private static $fields_type = array(
	 		'level_id'    => 'int',
	 		'level_name'  => 'string', //级别名称
	 		'level_info'  => 'string', //岗位信息
	 		'memo'        => 'string', //备注
	 		'update_time' => 'string',
	 		'status'      => 'int',    //状态:0无效1有效
	 );
	 	
	 /**
	 * 允许修改的字段，排除主键的其它字段
	 */
	 private static $update_fields = array(
	 		'level_name'  => 'string' ,
	 		'level_info'  => 'string' ,
	 		'memo'        => 'string' ,
	 		'update_time' => 'string' ,
	 		'status'      => 'int' ,
	 );
	
	 private function __construct() {}
	 
	 public static function getInstance() {
	 	if(empty(self::$instance)) {
	 		self::$instance = new self();
	 	}
	 	return self::$instance;
	 }
	 
	 
	 
}

