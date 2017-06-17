<?php
namespace Atom\Package\User;

use Atom\Package\Common\SqlBase;

class UserWorkInfo extends SqlBase {
	private static $instance = NULL ;
	private static $table    = 'user_work_info';
	private static $pk       = 'id';
	private static $col      = 'id, user_id, job_level_id, job_title_id, position, redmineid, mls_id, mls_nickname, others';
	private static $db_name  = SqlBase::DB_STAFF;
	/**
	 * 所有所有字段及类型
	 */
	private static $fields_type = array(
			'id'           => 'int',
			'user_id'      => 'int',    //员工id
			'job_level_id' => 'int',    //职位级别
			'job_title_id' => 'int',    //职位title
			'position'     => 'string', //工位
			'redmineid'    => 'int',    //redmine id
			'mls_id'       => 'int',    //美丽说id
			'mls_nickname' => 'string', //美丽说昵称
			'others'       => 'string', //其他信息 json格式存入
	);
		
	/**
	 * 允许修改的字段，排除主键的其它字段
	 */
	 private static $update_fields = array(
	 		'job_level_id' => 'int' ,
	 		'job_title_id' => 'int' ,
	 		'position'     => 'string' ,
	 		'redmineid'    => 'int' ,
	 		'mls_id'       => 'int' ,
	 		'mls_nickname' => 'string' ,
	 		'others'       => 'string' ,
	 );	
	
	 private function __construct() {}
	 
	 public static function getInstance() {
	 	if(empty(self::$instance)) {
	 		self::$instance = new self();
	 	}
	 	return self::$instance;
	 }
	 
	 
	
}