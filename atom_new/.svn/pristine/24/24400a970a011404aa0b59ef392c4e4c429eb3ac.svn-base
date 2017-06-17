<?php
namespace Atom\Package\User;

use Atom\Package\Helper\StaffDBHelper;
use Atom\Package\Common\SqlBase;
/**
 * 用户头像 user_avatar
 * @author wulianglong
 * @data 2015-4-7 下午5:37:16
 */
class UserAvatar extends SqlBase{
	
	private static $instance = NULL;
	private static $table    = 'user_avatar';	
	private static $pk       = 'id';
	private static $col      = 'id, user_id, avatar_src, avatar_big, avatar_middle, avatar_small, update_time, status';
	private static $db_name  = SqlBase::DB_STAFF;
	
	/** 所有字段及类型 **/
	private static $fields_type = array(
			'id'            => 'int',
			'user_id'       => 'int',    //员工id
			'avatar_src'    => 'string', //原始头像
			'avatar_big'    => 'string', //200*200
			'avatar_middle' => 'string', //100*100
			'avatar_small'  => 'string', //60*60
            'local_src'     => 'string', //原始头像(本地)
            'local_big'     => 'string', //200*200(本地)
            'local_middle'  => 'string', //100*100(本地)
            'local_small'   => 'string', //60*60(本地)
			'update_time'   => 'string',
			'status'        => 'int',    //状态:0无效1有效
	);	
	/** 允许修改的字段 **/
	private static $update_fields = array(
			'user_id'       => 'int' ,
			'avatar_src'    => 'string' ,
			'avatar_big'    => 'string' ,
			'avatar_middle' => 'string' ,
			'avatar_small'  => 'string' ,
            'local_src'     => 'string' ,
            'local_big'     => 'string' ,
            'local_middle'  => 'string' ,
            'local_small'   => 'string' ,
			'update_time'   => 'string' ,
			'status'        => 'int' ,
	);
	
	private function __construct() {}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * 修改状态,即删除功能
	 * @param string $user_id
	 * @return boolean|number
	 */
	public function updateStatus($user_id = '') {
		$user_id = intval($user_id);
		if(empty($user_id)) {
			return FALSE;
		}
	
		$sql = 'update ' . self::$table . ' set status = 0 where user_id = :_user_id';
		$sqlData = array(
				'_user_id' => $user_id,
		);
		
		return StaffDBHelper::getConn()->write($sql, $sqlData);
	}
	
	
}