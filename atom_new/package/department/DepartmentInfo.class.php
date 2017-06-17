<?php
namespace Atom\Package\Department;

use Atom\Package\Common\SqlBase;
class DepartmentInfo extends SqlBase {
	
	private static $instance = NULL ;
	private static $table    = 'department_info';
	private static $pk       = 'depart_id';
	private static $col      = 'depart_id, depart_name, depart_info, depart_level, parent_id, child_id, memo, update_time, is_official, is_virtual, level, status';
	private static $db_name  = SqlBase::DB_STAFF;
	
	/**
	  * 所有所有字段及类型
	 */
	 private static $fields_type = array(
	 		'depart_id'    => 'int',
	 		'depart_name'  => 'string', //部门name
	 		'depart_info'  => 'string', //部门信息
	 		'depart_level' => 'int',    //部门级别
	 		'parent_id'    => 'int',    //上级部门id
	 		'child_id'     => 'string', //子部门id
	 		'memo'         => 'string', //备注
	 		'update_time'  => 'string',
	 		'is_official'  => 'int',    //是否为正式部门:0不是1是
	 		'is_virtual'   => 'int',    //是否为虚拟部门:0不是1是
	 		'level'        => 'int',    //级别
	 		'status'       => 'int',    //状态:0无效1有效
	 );
	 	
	 /**
	 * 允许修改的字段，排除主键的其它字段
	 */
	 private static $update_fields = array(
	 		'depart_name'  => 'string' ,
	 		'depart_info'  => 'string' ,
	 		'depart_level' => 'int' ,
	 		'parent_id'    => 'int' ,
	 		'child_id'     => 'string' ,
	 		'memo'         => 'string' ,
	 		'update_time'  => 'string' ,
	 		'is_official'  => 'int' ,
	 		'is_virtual'   => 'int' ,
	 		'level'        => 'int' ,
	 		'status'       => 'int' ,
	 );	
	
	 private function __construct() {}
	 
	 public static function getInstance() {
	 	if(empty(self::$instance)) {
	 		self::$instance = new self();
	 	}
	 	return self::$instance;
	 }
}