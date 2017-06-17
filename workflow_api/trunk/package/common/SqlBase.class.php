<?php 
namespace WorkFlowApi\Package\Common;

use WorkFlowApi\Package\Helper\StaffDBHelper;
use WorkFlowApi\Package\Helper\CoreDBHelper;
use WorkFlowApi\Package\Helper\AdministrationDBHelper;
use WorkFlowApi\Package\Helper\HrDBHelper;
use WorkFlowApi\Package\Helper\RecruitDBHelper;
use WorkFlowApi\Package\Helper\RoutineDBHelper;

/**
 * 使用SQL编写package基类
 * 
 * 实现原理：反射
 * 供单例模式的子类继承，子类继承此类的方法后，可直接使用此类提供的通用方法。
 * 此类的方法被调用前会调用init()方法为SqlBase的属性赋值，赋值使用的是反射原理，获取子类默认属性，因此子类必须提供 init()方法中使用到的属性
 * 
 * @author wulianglong
 * @date 2015-4-16 下午3:53:28
 */
abstract class SqlBase {
	
	private static $table         = NULL;  //表名 string
	private static $pk            = NULL;  //主键
	private static $col           = NULL;  //默认查询字段 string
	private static $fields_type   = NULL;  //表的所有字段及类型 array
	private static $update_fields = NULL;  //所有允许修改的字段 array
	private static $db_name       = NULL;  //数据库名称
	
	/**
	 * 数据库
	 */
	const DB_CORE           = 'speed_core';
	const DB_ADMINISTRATION = 'speed_administration';
	const DB_HR             = 'speed_hr';
	const DB_RECRUIT        = 'speed_recruit';
	const DB_ROUTINE        = 'speed_routine';
	const DB_STAFF          = 'speed_staff';
	
	/**
	 * 调用此类的方法前，初史化SqlBase属性
	 * @param object $subClass  object || __CLASS__
	 * @return boolean
	 */
	private static function init($subClass) {

		$ref = new \ReflectionClass($subClass);		
		$sdp = $ref->getDefaultProperties();

		if(!isset($sdp['table']) || !isset($sdp['col']) || !isset($sdp['fields_type']) || !isset($sdp['update_fields']) || !isset($sdp['pk']) || !isset($sdp['db_name'])) {
			return FALSE;
		}
		
		self::$table         = $sdp['table'];
		self::$pk            = $sdp['pk'];
		self::$col           = $sdp['col'];		
		self::$fields_type   = $sdp['fields_type'];		
		self::$update_fields = $sdp['update_fields'];	
		self::$db_name       = $sdp['db_name'];
		
		return TRUE;	
	}
	
	/**
	 * 获取数据库链接
	 */
	protected static function getConn($db_name = '') {
		
		if(!empty($db_name)) {
			self::$db_name = $db_name;
		}
		
		switch(self::$db_name){
			case self::DB_CORE:
				return CoreDBHelper::getConn();
			case self::DB_ADMINISTRATION:
				return AdministrationDBHelper::getConn();
			case self::DB_HR:
				return HrDBHelper::getConn();
			case self::DB_RECRUIT:
				return RecruitDBHelper::getConn();
			case self::DB_ROUTINE:
				return RoutineDBHelper::getConn();
			case self::DB_STAFF :
				return StaffDBHelper::getConn();
			default:
				return NULL;
		}	
	}
	
	
	/**
	 * 添加一个数据
	 * @param array $params 添加的数据
	 * @param array $not_null table规定必须要插入的字段,如  array('user_id', 'abc', 'etc')
	 * @return boolean|Ambigous <boolean, string> 失败返回false, 成功返回插入的主键
	 */
	public function add($params, $not_null = array()) {
		if(!self::init($this)) {
			return FALSE;
		}
		
		if(!is_array($params) || empty($params)) {
			return FALSE;
		}
		$params = array_intersect_key($params, self::$fields_type);
		if(empty($params)) {
			return FALSE;
		}
		
		if(!empty($not_null)) {
			foreach ($not_null as $value) {
				if(!isset($params[$value])) {
					return FALSE;
				}
			}
		}

		$addsql = SqlUtils::addSql($params, self::$fields_type);
		$sql = 'insert into ' . self::$table . $addsql['sql'];
		$sqlData = $addsql['sqlData'];
	
		$ret = self::getConn()->write($sql, $sqlData);
		return $ret ? self::getConn()->getInsertId() : FALSE;
	}
	
	/**
	 * 添加或修改一个数据
	 * @param array $params
	 * @param array $not_null 若插入一个数据缺少某一字段这个数据就无意义了，这个字段适合写在这里，如 array('user_id', 'abc', 'etc')
	 * @return boolean|Ambigous <boolean, number> 参数错误返回false, 插入返回插入的id，修改返回影响的行数
	 */
	public function saveOrUpdate($params, $not_null = array()) {
		if(!self::init($this)) {
			return FALSE;
		}		
		
		if(!is_array($params) || empty($params)) {
			return FALSE;
		}
		$params = array_intersect_key($params, self::$fields_type);
		if(empty($params)) {
			return FALSE;
		}
		if(!empty($not_null)) {
			foreach ($not_null as $value) {
				if(!isset($params[$value])) {
					return FALSE;
				}
			}
		}
		
		$do_what = 'UPDATE';
		if(isset($params[self::$pk]) && !empty($params[self::$pk])) {  //确认数据是否真的存在
			$data = self::getByPk($params[self::$pk]);
			if(!$data || empty($data)) {
				$do_what = 'SAVE';
			}
		}
		
		$return = FALSE;
		switch ($do_what) {
			case 'SAVE':
				$return = self::add($params, $not_null);
				break;
			
			case 'UPDATE':
				$return = self::updateByPk($params);
				break;
			
			default :
				break;
		}		

		return $return;
	}
	
	/**
	 * 逻辑删除或逻辑恢复
	 * @param array $where 删除条件，在sql中会被使用and连接
	 * @param string $mark 表中的逻辑删除字段 默认为status
	 * @param number $mark_as 邮件逻辑删除标记字段修改为 $mark_as
	 * @return boolean|number 参数错误返回false，则否返回影响的行数
	 */
	public function deleteLogical($where = array(), $mark = 'status', $mark_as = 0) {
		if(!self::init($this)) {
			return FALSE;
		}
		
		//必须要有删除条件，数据字必须要有逻辑删除标记字段
		if(!is_array($where) || empty($where) || empty($mark) || !isset(self::$fields_type[$mark])) {
			return FALSE;
		}
		$where = array_intersect_key($where, self::$fields_type);
		if(empty($where)) {
			return FALSE;
		}
		
		$sql = ' update ' . self::$table . " set `{$mark}` = {$mark_as} where 1 ";
		$sqlData = array();
		
		foreach ($where as $key => $value) {
			switch (self::$fields_type[$key]) {
				case 'int' :
					$sql .= " and `{$key}` = :_{$key} ";
					$sqlData["_{$key}"] = $value;
					break;
					
				case 'string' :
					$sql .= " and `{$key}` = :{$key} ";
					$sqlData[$key] = $value;
					break;

				default :
					break;
			}//switch 
		}
		
// 		print_r($sql);var_dump($sqlData); exit();
		return self::getConn()->write($sql, $sqlData);
	}
	
	/**
	 * 物理删除
	 * @param array $where 删除条件
	 * @return boolean|number 参数错误返回false,否则返回影响的行数
	 */
	public function deletePhysical($where = array()) {
		if(!self::init($this)) {
			return FALSE;
		}		
		
		//必须要有删除条件
		if(!is_array($where) || empty($where)) {
			return FALSE;
		}
		$where = array_intersect_key($where, self::$fields_type);
		if(empty($where)) {
			return FALSE;
		}
		
		$sql = ' delete from ' . self::$table . ' where 1 ';
		$sqlData = array();
		
		foreach ($where as $key => $value) {
			switch (self::$fields_type[$key]) {
				case 'int' :
					$sql .= " and `{$key}` = :_{$key} ";
					$sqlData["_{$key}"] = $value;
					break;
					
				case 'string' :
					$sql .= " and `{$key}` = :{$key} ";
					$sqlData[$key] = $value;
					break;

				default :
					break;
			}//switch 
		}
		
// 		print_r($sql);var_dump($sqlData); exit();
		return self::getConn()->write($sql, $sqlData);
	}
	
	/**
	 * 根据主键修改
	 * @param array $params 参数，必须要有主键
	 * @return boolean|number 参数错误返回false，否则返回影响的行数
	 */
	public function updateByPk($params = array()) {
		if(!self::init($this)) {
			return FALSE;
		}		
		
		//必须要有主键
		if(empty($params) || !isset($params[self::$pk]) || empty($params[self::$pk])) {
			return FALSE;
		}		
		$params = array_intersect_key($params, self::$fields_type);
		
		$sql = ' update ' . self::$table . ' set ';
		$sqlData = array();
		foreach ($params as $key => $value) {
			if(isset(self::$update_fields[$key])) {
				switch (self::$update_fields[$key]) {
					case 'int':
						$sql .= "`{$key}` = :_{$key},";
						$sqlData["_{$key}"] = intval(trim($value));
						break;
					case 'string':
						$sql .= "`{$key}` = :{$key},";
						$sqlData[$key] = $value;
						break;
					default:
						break;
				}//switch
			}
		}

		$sql = rtrim($sql, ',');
		
		$sql .= ' where `' . self::$pk . '` = :_' . self::$pk;
		$sqlData['_'.self::$pk] = $params[self::$pk];	
		
// 		print_r($sql);print_r($sqlData);exit('');
		return self::getConn()->write($sql, $sqlData);
	}
	
	
	/**
	 * 更新数据库
	 * @param array $params 需要更新的数据
	 * @param array $where where 条件，将被and连接
	 * @return boolean|number 缺少条件返回false,否则返回影响的行数
	 */
	public function update($params = array(), $where = array()) {
		if(!self::init($this)) {
			return FALSE;
		}		
		
		//必须要有修改条件
		if(!is_array($where) || empty($where)) {
			return FALSE;
		}
		$params = array_intersect_key($params, self::$fields_type);		
		if(empty($params)) {
			return FALSE;
		}

		$sql = ' update ' . self::$table . ' set ';
		$sqlData = array();
		foreach ($params as $key => $value) {
			if(isset(self::$update_fields[$key])) {
				switch (self::$update_fields[$key]) {
					case 'int':
						$sql .= "`{$key}` = :_{$key},";
						$sqlData["_{$key}"] = intval(trim($value));
						break;
					case 'string':
						$sql .= "`{$key}` = :{$key},";
						$sqlData[$key] = $value;
						break;
					default:
						break;
				}//switch
			}
		}

		$sql = rtrim($sql, ',');

		//修改条件
		$sql .= ' where 1 ';
		foreach ($where as $k => $v) {
			if(isset(self::$fields_type[$k])) {
				switch (self::$fields_type[$k]) {
					case 'int':
						$sql .= " AND `$k` = :_w$k";
						$sqlData["_w$k"] = $v;
						break;
					case 'string':
						$sql .= " AND `$k` = :w$k";
						$sqlData['w'.$k] = $v;
						break;
					default:
						break;
				}
			}
		}
		
// 		print_r($sql);print_r($sqlData);exit();
		return self::getConn()->write($sql, $sqlData);
	}
	
	/**
	 *  根据主键查找数据 NOTE: 主键是表结构定义的primary key
	 * @param number $primary_key 主键值
	 * @return boolean|multitype:unknown mixed 参数错误返回false, 否则返回找到的结果
	 */
	public function getByPk($primary_key = 0) {
		if(!self::init($this)) {
			return FALSE;
		}		
		
		$primary_key = intval($primary_key);
		if(empty($primary_key)) {
			return FALSE;
		}
		
		$sql = 'select ' . self::$col . ' from ' . self::$table . ' where `' . self::$pk . '`= :_' . self::$pk  ;
		$sqlData = array(
			'_'.self::$pk => $primary_key,
		);

// 		print_r($sql);var_dump($sqlData);exit();
		return self::getConn()->read($sql, $sqlData);
	}
	
	/**
	 * 获取所有数据 默认返回 2000条
	 * @param number $start
	 * @param number $num
	 * @return 查询结果
	 */
	public function getAll($start = 0, $num = 2000, $hash = '') {
		if(!self::init($this)) {
			return FALSE;
		}		
		
		$start = intval($start);
		$num = intval($num);
		$sql = ' select ' . self::$col . ' from ' . self::$table . ' limit ' . $start . ',' . $num;
		
		$hash = array_key_exists($hash, self::$fields_type) ? $hash : '';
		
		return self::getConn()->read($sql, array(), FALSE, $hash);
	}
	
	/**
	 * 查询列表或分页
	 * @param array $where
	 * @param int $start
	 * @param int $num
	 * @return boolean|multitype:unknown mixed
	 */
	public function getList($where = array(), $start = -1, $num = 10, $hash = '') {
		if(!self::init($this)) {
			return FALSE;
		}		
		
		if(!is_array($where) || empty($where)) {
			return FALSE;
		}
		$where = array_intersect_key($where, self::$fields_type);
		if(empty($where)) {
			return FALSE;
		}
		
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
		
		$start = intval($start);
		$num   = intval($num);
		if($start > -1 && $num > 0) {
			$sql .= " limit {$start}, {$num}";
		}
		
		$hash = array_key_exists($hash, self::$fields_type) ? $hash : '';
		
// 		print_r($sql);print_r($sqlData);exit();		
		return self::getConn()->read($sql, $sqlData, FALSE, $hash);
	}
	
	
	/**
	 * 条件记数
	 * @param array $where 可空
	 * @return 数据个数
	 */
	public function getCount($where = array()) {
		if(!self::init($this)) {
			return FALSE;
		}		
		
		$where = array_intersect_key($where, self::$fields_type);
		
		$sql = ' select count(1) as num from ' . self::$table . ' where 1 ';
		$sqlData = array();
		if(is_array($where) && !empty($where)) {
			foreach ($where as $key => $value) {
				switch (self::$fields_type[$key]) {
					case 'int':
						$sql .= " and `$key`=:_$key";
						$sqlData['_'.$key] = intval(trim($value));						
						break;
						
					case 'string':
						$sql .= " and `$key`=:$key";
						$sqlData[$key] = $value;
						break;
						
					default:
						break;
					
				}//switch
			}//foreach
			
		}//if

// 		print_r($sql);var_dump($sqlData);exit();
		
		$result = self::getConn()->read($sql, $sqlData);
		if(!empty($result) && is_array($result)) {
			$count = array_pop($result);
			$count = $count['num'];
		}else {
			$count = 0;
		}
		return $count;
	}
	
	/**
	 * 使用in获取数据  查询语句：where $what in ( $want )
	 * @param array $want 如： array(1,2,3,4)
	 * @param string $what 如：user_id
	 * @param string $type 只能为 int 或 string
	 * @return 参数错误返回false，否则返回获取的结果
	 */
	public function getUseIn($want = array(), $what = '', $type = 'int', $where = array(), $hash = '') {
		if(!self::init($this)) {
			return FALSE;
		}		

		if(!is_array($want) || empty($what) || !isset(self::$fields_type[$what])) {
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

		$sql = ' select ' . self::$col . ' from ' . self::$table . ' where `' . $what . '` in(' . $want . ')';
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
		
// 		print_r($sql);print_r($sqlData);exit();
		return self::getConn()->read($sql, $sqlData, FALSE, $hash);
	}
	
	/**
	 * 模糊查询 where xx like 'xxx%'
	 * NOTE: 为保证like可以使用到索引，仅支持 xxx% 查询, 不支持形如 %xxx 查询
	 * @param string $field 字段
	 * @param string $value 值
	 * @param string $hash 数组下标字段
	 * @return boolean|multitype:unknown mixed
	 */
	public function getUseLike($field = '', $value = '', $hash = '', $where = array()) {
		if(!self::init($this)) {
			return FALSE;
		}		 
		
		$value = trim($value);
		 
		if(empty($field) || empty($value) || !array_key_exists($field, self::$fields_type)) {
			return FALSE;
		}
	
		$sql = ' select ' . self::$col . ' from ' . self::$table . ' where ' . " `{$field}` like :value ";
		$sqlData = array('value' => $value . '%');
		
		$hash = array_key_exists($hash, self::$fields_type) ? $hash : '';
		
		
		return self::getConn()->read($sql, $sqlData, FALSE, $hash);
	}
	
	/**
	 * 根据user_id获取数据
	 * @param array $where 查询条件 user_id必须
	 * @return 参数错误返回false，则否返回查询数据
	 */
	public function getByUserId($where = array(), $hash = '') {
		if(!self::init($this)) {
			return FALSE;
		}		
		
		if(!is_array($where) || empty($where) || !isset($where['user_id'])) {
			return FALSE;
		}	
		$where = array_intersect_key($where, self::$fields_type);

		$sql = ' select ' . self::$col . ' from ' . self::$table . ' where 1';
		$sqlData = array();
		
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
		}		

		$hash = array_key_exists($hash, self::$fields_type) ? $hash : '';
		
// 		print_r($sql);var_dump($sqlData);exit();
		return self::getConn()->read($sql, $sqlData, FALSE, $hash);
	}
	
}



