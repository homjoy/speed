<?php
namespace Atom\Package\Department;

use Atom\Package\Common\SqlBase;

class DepartmentLeader extends SqlBase {

	private static $instance = NULL ;
	private static $table    = 'department_leader';
	private static $pk       = 'leader_id';
	private static $col      = 'leader_id, depart_id, user_id, update_time, status';
	private static $db_name  = SqlBase::DB_STAFF;
	
	/**
	  * 所有所有字段及类型
	 */
	 private static $fields_type = array(
	 		'leader_id'   => 'int',
	 		'depart_id'   => 'int',    //部门id
	 		'user_id'     => 'int',    //用户id
	 		'update_time' => 'string',
	 		'status'      => 'int',    //状态:0无效1有效
	 );
	 	
	 /**
	 * 允许修改的字段，排除主键的其它字段
	 */
	 private static $update_fields = array(
	 		'depart_id'   => 'int' ,
	 		'user_id'     => 'int' ,
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