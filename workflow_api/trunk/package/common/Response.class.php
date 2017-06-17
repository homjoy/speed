<?php
namespace WorkFlowApi\Package\Common;

/**
 * 系统状态
 * @author hepang@meilishuo.com
 * @since 2014-12-04
 */

class Response {

	private static $instance;

    public static function getInstance(){
        if (empty(self::$instance)) {
            self::$instance = new self(); 
        }   
        return self::$instance;
    }

    public function __construct() {

    }

	const SUCCESS = 0;
	const UNKNOWN = 10000;

	//参数型错误
	const PARAM_EMPTY              = 10001;
	const PARAM_INVALID            = 10002;
	const PARAM_ERROR_TYPE         = 10003;
	const PARAM_ERROR_FORMAT       = 10004;
	const PARAM_NOT_INTEGER        = 10005;
	const PARAM_OUT_OF_RANGE       = 10006;
	const PARAM_EXCEED_LENGTH      = 10007;
	const SCHAME_EMPTY             = 10014;
	const SCHAME_ITEM_ERROR_TYPE   = 10015;
	const SCHAME_ITEM_NOT_EMPTY    = 10016;
	const SCHAME_ITEM_INVALID_ENUM = 10017;
	const SCHAME_ITEM_LESS_MIN     = 10018;
	const SCHAME_ITEM_MORE_MAX     = 10019;
	const SCHAME_FORMAT_ERROR      = 10020;
	const DS_DATA_TYPE_ERROR       = 10021;
	const DS_DATA_TYPE_NOT_ALIKE   = 10022;
	const MESSAGE_USEING_DATA      = 10023;

	//身份验证相关
	const AUTH_PARAM_ABSENT  = 20001;
	const AUTH_WRONG_APPKEY  = 20002;
	const AUTH_SIGNATURE_ERR = 20003;
	const AUTH_TIME_EXPIRED  = 20004;

	//内容相关
	const CONTENT_EMPTY		 = 30001;

	//路由相关
	const MODULE_NOT_EXISTS  = 40001;
	const METHOD_NOT_EXISTS  = 40002;

	//数据库错误
	const DB_CONNET_ERROR    = 50001;
	const DB_NO_DATA         = 50002;
	const DB_INSERT_ERROR    = 50003;
	const DB_UPDATE_ERROR    = 50004;
	const DB_DELETE_ERROR    = 50005;
	const DB_QUERY_ERROR     = 50006;
	const DATA_GET_EXCEP     = 50007;
	const DATA_INSERT_EXCEP  = 50008;
	const DATA_UPDATE_EXCEP  = 50009;
	const DATA_DELETE_EXCEP  = 50010;
	const DS_DATA_NO_DATA    = 50011;
	const DATA_NO_UPDATE     = 50012;

	//自动接口错误
	const FACETED_QUERY_ERROR = 60001;
	const NOVA_CALLBACK_ERROR = 60002;

	//锁相关错误
	const LOCK_IS_OCCUPIED   = 70001;
	//操作权限错误
	const ROLE_CHECK_FAILD   = 90001;

	public static $ErrMsg = array(
		self::UNKNOWN                  => '未知错误',
		self::PARAM_EMPTY              => '参数不能为空',
		self::PARAM_INVALID            => '非法参数',
		self::PARAM_ERROR_TYPE         => '参数类型错误',
		self::PARAM_ERROR_FORMAT       => '参数格式错误',
		self::PARAM_NOT_INTEGER        => '参数必须为整数',
		self::PARAM_OUT_OF_RANGE       => '参数值超出范围',
		self::PARAM_EXCEED_LENGTH      => '参数长度超出限制',
		self::DB_CONNET_ERROR          => '数据库连接错误',
		self::DB_NO_DATA               => '没有获取到数据',
		self::DB_INSERT_ERROR          => '数据库插入失败',
		self::DB_UPDATE_ERROR          => '数据库更新失败',
		self::DB_DELETE_ERROR          => '数据库删除失败',
		self::DB_QUERY_ERROR           => '数据库查询失败',
		self::DATA_GET_EXCEP           => '数据读取发生异常',
		self::DATA_INSERT_EXCEP        => '数据写入发生异常',
		self::DATA_UPDATE_EXCEP        => '数据更新发生异常',
		self::DATA_DELETE_EXCEP        => '数据删除发生异常',
		self::DATA_NO_UPDATE           => '数据没有更新',
		self::SCHAME_EMPTY             => '没有数据库表格式定义',
		self::SCHAME_ITEM_ERROR_TYPE   => '数据项不符合数据库表字段类型',
		self::SCHAME_ITEM_NOT_EMPTY    => '数据库表非空字段没有传值',
		self::SCHAME_ITEM_INVALID_ENUM => '数据项值不是可用的枚举值',
		self::SCHAME_ITEM_LESS_MIN     => '数据项值小于允许的最小值',
		self::SCHAME_ITEM_MORE_MAX     => '数据项值大于允许的最大值',
		self::SCHAME_FORMAT_ERROR      => '数据库表格式定义错误',
		self::AUTH_PARAM_ABSENT        => '缺少参数',
		self::AUTH_WRONG_APPKEY        => 'appKey错误',
		self::AUTH_SIGNATURE_ERR       => '签名值错误',
		self::AUTH_TIME_EXPIRED        => '请求时间戳过期',
		self::DS_DATA_TYPE_ERROR       => '数据源类型有误',
		self::DS_DATA_NO_DATA          => '相应数据源不存在',

		self::DS_DATA_TYPE_NOT_ALIKE   => '数据源类型不一致',
		self::FACETED_QUERY_ERROR      => '中间层查询失败',
		self::NOVA_CALLBACK_ERROR      => 'nova框架返回有误',
		self::LOCK_IS_OCCUPIED         => '锁已被占用',
		self::MESSAGE_USEING_DATA      => '数据正被消息推送使用',

		self::CONTENT_EMPTY      	   => '没有数据',
		self::MODULE_NOT_EXISTS        => '指定模块不存在',
		self::METHOD_NOT_EXISTS        => '请求方法不存在',

		self::ROLE_CHECK_FAILD        => '没有操作权限',
	);

	/*
	 *  错误代码返回结构
	 *
	 *  @param $code 错误代码 int
	 *  @param $params 出错参数，多个参数用逗号分隔 string
	 *  @retsult array  
	 */
	public static function gen_error($code, $msg = '', $detail = '') {
		$code = intval($code);
		$ret = array();

		if (!isset(self::$ErrMsg[$code])) {
			$code = 10000;
		}

		if (!empty($msg)) {
			if (is_string($msg)) {
				$msg = trim($msg);
			}
		}else{
			$msg = self::$ErrMsg[$code];
		}

		$ret['code'] 		= self::get_code($code);
		$ret['error_code'] 	= $code;
		$ret['error_msg'] 	= $msg;
		!empty($detail) && $ret['error_detail']	= $detail;

		return $ret;
	}

	/*
	 *  错误代码返回结构
	 *
	 *  @param $code 错误代码 int
	 *  @param $params 出错参数，多个参数用逗号分隔 string
	 *  @retsult array  
	 */
	public static function gen_success($data) {

		$ret = array();

		$ret['code'] 		= 200;
		$ret['error_code'] 	= 0;
		$ret['data'] 	= $data;

		return $ret;
	}

	/*
	 *  追加参数到错误代码
	 *
	 *  @param $ret 错误代码 array('code'=>, 'param'=>)
	 *  @param $params 出错参数，多个参数用逗号分隔 string
	 *  @retsult array  
	 */
	public static function append_ret($ret, $params = '') {
		if (empty($params)) {
			return $ret;
		}

		$ret['param'] = isset($ret['param']) ? ($ret['param'] . ',' . $params) : $params;
		if (isset($ret['code'])) {
			$ret = self::gen_ret($ret['code'], $ret['param']);
		}

		return $ret;
	}

	/*
	 *  获取参加code码
	 *
	 *  @param $ret 错误代码 array('code'=>, 'param'=>)
	 *  @param $params 出错参数，多个参数用逗号分隔 string
	 *  @retsult array  
	 */
	private static function get_code($code=0){
		if ($code > 50000) {
			return 500;
		}elseif ($code > 10000) {
			return 400;
		}
	}
}
