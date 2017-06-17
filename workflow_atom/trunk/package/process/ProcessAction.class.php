<?php

?><?php
namespace WorkFlowAtom\Package\Process;

/**
 * 工作流节点动作
 * @package WorkFlowAtom\Package\Process
 * @author yixiangwang@meilishuo.com
 * @since 2015-07-14
 */

use WorkFlowAtom\Package\Common\DbAdapter;

class ProcessAction {

    private static $instance = null;
    private static $conn;

    private static $tableName = 'workflow_action';
    private static $col = array('id', 'process_id', 'action_name', 'action_behavior', 'sort', 'action_type', 'status');
    private static $pk = 'id';
    private static $fields = array(
		'id'				=> 0,
		'process_id'		=> 0,
		'action_name'		=> '',
		'action_behavior'	=> '',
		'sort'				=> 0,
		'action_type'		=> 1,
		'status'			=> 0
    );
    private static $update_fields = array(
		'process_id'		=> 'int',
		'action_name'		=> 'string',
		'action_behavior'	=> 'string',
		'sort'				=> 'int',
		'status'			=> 'int'
    );

    private function __construct() {}

    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance = new self();
            self::$conn =  new \Pixie\Connection(new DbAdapter('workflow'));
        }
        return self::$instance;
    }

    public static function getFields()
    {
        return self::$fields;
    }

    /**
     * 获取单条信息
     * @param $id
     * @param array $fields
     * @return array
     */
    public function getDataById($id, $fields = array())
    {
        if (empty($fields)) {
            $fields = static::$col;
        }

        //查询
        $qb = self::$conn->getQueryBuilder();
        $ret = $qb->table(static::$tableName)
            ->select(static::$col)
            ->find($id, static::$pk);

        return $ret;
    }

    /**
     * 查询列表
     */
    public function getDataList(array $params = array())
    {

        //查询
        $qb = self::$conn->getQueryBuilder();
        $ret = $qb->table(static::$tableName)
            ->select(static::$col);
            //->offset(($page-1)*$pageSize)
            //->limit($pageSize);

        //查询条件
        if (isset($params['process_id'])) {
            if (is_array($params['process_id'])) {
                $ret->whereIn('process_id', $params['process_id']);
            }else{
                $ret->where('process_id', '=', $params['process_id']);
            }
        }
		
		//查询条件
        if (isset($params['status'])) {
            $ret->where('status', '=', $params['status']);
        }

        $ret->hash(self::$pk);
		$ret->orderBy(self::$pk,'ASC');
        return $ret->get();
    }

    /**
     * 添加
     */
    public static function insert($params) {

        if (empty($params['process_id']) || empty($params['action_name']) || empty($params['action_behavior']) || empty($params['action_type'])){
            return FALSE;
        }

        $params = array_intersect_key($params, self::$fields);
        $params = array_merge(self::$fields, $params);
        unset($params[self::$pk]);

        $qb = self::$conn->getQueryBuilder();
        $ret = $qb->table(static::$tableName);
        return $ret->insert($params);
    }

    /**
     * 更新
     */
    public static function update($params) {

        if (!isset($params[self::$pk])){
            return FALSE;
        }

        $params = array_intersect_key($params, self::$fields);
        $pkid = $params[self::$pk];
        unset($params[self::$pk]);

        $qb = self::$conn->getQueryBuilder();
        $ret = $qb->table(static::$tableName);
		
		if(is_array($pkid)) {
			$ret = $ret->whereIn(self::$pk, $pkid);
		}else {
			$ret = $ret->where(self::$pk, $pkid);
		}
        
        return $ret->update($params);
    }
	
	//删除
	public function deleteDataById($id){

        //查询
        $qb = self::$conn->getQueryBuilder();
        $ret = $qb->table(static::$tableName)
            ->whereIn(self::$pk, $id)
			->delete();

        return $ret;
    }
} 
