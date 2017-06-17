<?php
namespace Atom\Package\User;

use Atom\Package\Common\SqlBase;

class UserJobTitle extends SqlBase {
	
	private static $instance = NULL ;
	private static $table    = 'user_job_title';
	private static $pk       = 'title_id';
	private static $col      = 'title_id, depart_id, title_name, title_info, memo, update_time, status';
	private static $db_name  = SqlBase::DB_STAFF;
	
	 /**
	  * 所有所有字段及类型
	 */
	 private static $fields_type = array(
	 		'title_id'    => 'int',
	 		'depart_id'   => 'int',    //部门
	 		'title_name'  => 'string', //角色名称
	 		'title_info'  => 'string', //角色信息
	 		'memo'        => 'string', //备注
	 		'update_time' => 'string',
	 		'status'      => 'int',    //状态:0禁用1启用
	 );
	 	
	 /**
	 * 允许修改的字段，排除主键的其它字段
	 */
	 private static $update_fields = array(
	 		'depart_id'   => 'int' ,
	 		'title_name'  => 'string' ,
	 		'title_info'  => 'string' ,
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