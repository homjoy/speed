<?php
namespace Atom\Package\Common;

use Libs\Http\Util;
/**
 * sql 工具
 * @author wulianglong
 * @date 2015-4-15 下午2:54:58
 */
class SqlUtils {
	
	/**
	 * 拼接插入sql
	 */
	public static function addSql($params, $fields_type) {
		$sqlk = '(';
		$sqlv = '(';
		$sqlData = array();
		foreach ($params as $key => $value) {
			if(isset($fields_type[$key])) {
				switch ($fields_type[$key]) {
					case 'int' : 
						$sqlk             .= "$key, ";
						$sqlv             .= ":_$key, ";
						$sqlData['_'.$key] =  intval(trim($value));
						break;
						
					case 'string' : 
						$sqlk         .= "$key, ";
						$sqlv         .= ":$key, ";
						$sqlData[$key] = (string)trim($value);
						break;
					
					default:
						break;

				}//switch
			}//if
		}//foreach

		$sqlk = rtrim($sqlk, ', ') . ')';
		$sqlv = rtrim($sqlv, ', ') . ')';
		$sql = $sqlk . ' values ' . $sqlv;

		return array(
			'sql'     => $sql,
			'sqlData' => $sqlData
		);
		
	}
	
	/**
	 * 整型处理
	 */
	public static function formatInt($param = '', $rm_zero = TRUE) {
		if(empty($param)) {
			return FALSE;
		}
		
		if(!is_array($param)) {
			return intval($param);
		}
		
		//数组
		foreach ($param as $k => $v) {
			$v = intval($v);
			if(empty($v) && $rm_zero) {
				unset($param[$k]);
			}else {
				$param[$k] = $v;
			}
		}
		
		return $param;		
	}
	
	/**
	 * 字符串处理
	 */
	public static function formatString($param = '', $rm_empty = TRUE) {
		
		if(empty($param)) {
			return $param;
		}
		
		if(!is_array($param)) {
			$param = trim($param);
			return addslashes($param);
		}
		
		//数组
		foreach ($param as $k => $v) {
			$v = trim($v);
			if(empty($v) && $rm_empty) {
				unset($param[$k]);
			}else {
				$param[$k] = addslashes($v);
			}
		}
		
		if(empty($param)) {
			return FALSE;
		}
		
		return $param;
	}
	
}