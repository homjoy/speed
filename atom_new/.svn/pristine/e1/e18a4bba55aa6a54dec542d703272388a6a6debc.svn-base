<?php
namespace Atom\Package\User;

use Atom\Package\Common\SqlBase;
use Atom\Package\Common\SqlUtils;

class UserInfo extends SqlBase {
	
	private static $instance = NULL ;
	private static $table    = 'user_info';
	private static $pk       = 'user_id';
	private static $col      = 'user_id, depart_id, job_role_id, name_cn, name_en, mail, hire_time, positive_time, staff_id, gender, status, flag, graduation_time';
	private static $db_name  = SqlBase::DB_STAFF;
	
	 /**
	  * 所有所有字段及类型
	 */
	 private static $fields_type = array(
	 		'user_id'       => 'int',
	 		'depart_id'     => 'int',    //部门id
	 		'job_role_id'   => 'int',    //职位角色
	 		'name_cn'       => 'string', //汉语名字
	 		'name_en'       => 'string', //拼音
	 		'mail'          => 'string', //邮箱前缀
	 		'hire_time'     => 'string', //入职时间
	 		'positive_time' => 'string', //转正时间
	 		'staff_id'      => 'string', //工号
	 		'gender'        => 'int',    //性别0女1男
	 		'status'        => 'int',    //在职状态:1在职2已离职3重新入职
	 		'flag'          => 'int',    //标记:1实习2试用3正式4申请离职
            'graduation_time'=> 'string', //毕业时间
	 );
	 	
	 /**
	 * 允许修改的字段，排除主键的其它字段
	 */
	 private static $update_fields = array(
	 		'depart_id'     => 'int' ,
	 		'job_role_id'   => 'int' ,
	 		'name_cn'       => 'string' ,
	 		'name_en'       => 'string' ,
	 		'mail'          => 'string' ,
	 		'hire_time'     => 'string' ,
	 		'positive_time' => 'string' ,
	 		'staff_id'      => 'string' ,
	 		'gender'        => 'int' ,
	 		'status'        => 'int' ,
	 		'flag'          => 'int' ,
            'graduation_time'=> 'string',
	 );
	 
	 private function __construct() {}
	 
	 public static function getInstance() {
	 	if(empty(self::$instance)) {
	 		self::$instance = new self();
	 	}
	 	return self::$instance;
	 }
	 
	 /**
	  * 模糊查询 where xx like 'xxx%' and status in (xx) 
	  * NOTE: 为保证like可以使用到索引，仅支持 xxx%, 不支持 %xxx 查询
	  * @param string $field 字段
	  * @param string $value 查
	  * @param string $in  {YES | NO} YES在职，NO离职
	  * @return boolean|multitype:unknown mixed
	  */
	 public function getUseLike($field = '', $value = '', $in = '', $hash = '') {
	 	
	 	$value = trim($value);
	 	$value = addslashes($value);
	 	
	 	$like_fields = array('name_cn', 'name_en', 'mail', 'staff_id');  //允许使用like的字段
	 	if(empty($field) || empty($value) || !in_array($field, $like_fields)) {
			return FALSE;
		}	 

		if($in == 1) {
			$status_str = '1,3'; //在职
		}else if($in == 2) {
			$status_str = '2';  //离职
		}else {
			$status_str = '';  //所有
		}
		
		$sql = ' select ' . self::$col . ' from ' . self::$table . ' where ' . " `{$field}` like :val ";
		if(!empty($status_str)) {
			$sql .= " and `status` in ({$status_str})";
		}
		$sqlData = array('val' => '%' . $value . '%');
		
		$hash = array_key_exists($hash, self::$fields_type) ? trim($hash) : '';
		
// 		print_r($sql);print_r($sqlData);exit();
		
		return self::getConn(self::$db_name)->read($sql, $sqlData, FALSE, $hash);
	 }

	 
	 /**
	  * 使用in获取数据  查询语句：where $what in ( $want )
	  * @param array $want 如： array(1,2,3,4)
	  * @param string $what 如：user_id
	  * @param string $type 只能为 int 或 string
	  * @param string $in {1 | 2 | 其它} 1在职，2离职，否则查询所有 
	  * @return 参数错误返回false，否则返回获取的结果
	  */
	 public function getUseIn($want = array(), $what = '', $type = 'int', $where = array(), $hash = '', $in = '') {
	 	if(!is_array($want) || empty($what) || !isset(self::$fields_type[$what]) || !in_array($in, array(1, 2, ''))) {
	 		return FALSE;
	 	}
	 
	 	switch ($type) {
	 		case 'int':
	 			$want = SqlUtils::formatInt($want);
	 			break;
	 		case 'string':
	 			$want = SqlUtils::formatString($want);
	 			break;
	 		default:
	 			$want = FALSE;
	 			break;
	 	}
	 
	 	if(!$want || empty($want)) {
	 		return FALSE;
	 	}
	 
	 	$want = array_values($want);
	 	$want = "'" . implode("','", $want) . "'";
	 	
	 	if($in == 1) {  
	 		$status_str = '1,3'; //在职
	 	}else if($in == 2) {
	 		$status_str = '2';  //离职
	 	}else {
	 		$status_str = '';  //所有
	 	}
	 
	 	$sql = ' select ' . self::$col . ' from ' . self::$table . ' where `' . $what . '` in(' . $want . ') ';
	 	if(!empty($status_str)) {
	 		$sql .= ' and `status` in (' . $status_str . ')';
	 	}
	 	$sqlData = array();
	 
	 	$where = array_intersect_key($where, self::$fields_type);
	 	if(is_array($where) && !empty($where)) {
	 		foreach ($where as $key => $value) {
	 			switch (self::$fields_type[$key]) {
	 				case 'int':
	 					$sql .= " and `{$key}` = :_{$key} ";
	 					$sqlData["_{$key}"] = $value;
	 					break;
	 
	 				case 'string':
	 					$sql .= " and `{$key}` = :{$key} ";
	 					$sqlData[$key] = $value;
	 					break;
	 
	 				default:
	 					break;
	 			}//switch
	 		}	//foreach
	 	}
	 
	 	$hash = array_key_exists($hash, self::$fields_type) ? $hash : '';
	 
	 	//print_r($sql);print_r($sqlData);exit();
	 	return self::getConn(self::$db_name)->read($sql, $sqlData, FALSE, $hash);
	 }

	/**
	 * 根据user_id获取数据
	 * @param array $where 查询条件 user_id必须
	 * @return 参数错误返回false，则否返回查询数据
	 */
	public function getByMail($mail = '') {
		$mail = trim($mail);
		
		if(empty($mail)) {
			return FALSE;
		}

		$sql = ' select ' . self::$col . ' from ' . self::$table . ' where mail = :mail';
		$sqlData = array(
			'mail' => $mail,
		);

		return self::getConn(self::$db_name)->read($sql, $sqlData);
	}
	
	/**
	 * 查询列表或分页
	 * @param array $where
	 * @param int $start
	 * @param int $num
	 * @return boolean|multitype:unknown mixed
	 */
	public function getList($where = array(), $start = -1, $num = 10, $hash = '') {
		
		if(!is_array($where) || empty($where)) {
			return FALSE;
		}

		$where = array_intersect_key($where, self::$fields_type);
		if(empty($where)) {
			return FALSE;
		}

		$status_str = (isset($where['status']) && $where['status'] === 0) ? '2' : '1,3'; //在职离职
		unset($where['status']);
		
		$sql = ' select ' . self::$col . ' from ' . self::$table . ' where 1 ';
		$sqlData = array();
		foreach ($where as $key => $value) {
			switch (self::$fields_type[$key]) {
				case 'int':
					$sql .= " AND `$key`=:_$key";
					$sqlData['_'.$key] = $value;
					break;
	
				case 'string':
					$sql .= " AND `$key`=:$key";
					$sqlData[$key] = $value;
					break;
	
				default:
					break;
			}
		}
	
		$sql .= " and status in ({$status_str})";
		$start = intval($start);
		$num   = intval($num);
		if($start > -1 && $num > 0) {
			$sql .= " limit {$start}, {$num}";
		}
	
		$hash = array_key_exists($hash, self::$fields_type) ? $hash : '';
	
// 		print_r($sql);print_r($sqlData);exit();
		return self::getConn(self::$db_name)->read($sql, $sqlData, FALSE, $hash);
	}
	
	
	
}