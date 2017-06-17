<?php
namespace WorkFlowAtom\Package\Process;

/**
 * 任务处理流程
 * @author jingjingzhang@meilishuo.com
 * @since 2015-08-02
 */

use WorkFlowAtom\Package\Common\DbAdapter;

class Progress {

	private static $instance = null;
    private static $conn;

    private static $tableName = 'workflow_progress';
    private static $col = array('progress_id', 'task_id', 'process_id', 'action_type', 'current_user_id', 'status', 'progress_content', 'create_time');
    private static $pk = 'progress_id';
    private static $fields = array(
    	'progress_id'      => 0,
    	'task_id'          => 0,
    	'process_id'       => 0,
    	'action_type'      => 0,// 1、同意 2、驳回
    	'current_user_id'  => 0,
    	'status'           => 0,// 1 新建；2 待接收；3 处理中；4 完成；5 挂起；6 撤销；0 失效；
    	'progress_content' => '',
        'create_time'      => '',
    );
    private static $update_fields = array(
    	'task_id'          => 'int',
    	'process_id'       => 'int',
    	'action_type'      => 'int',
    	'current_user_id'  => 'int',
    	'status'           => 'int',
    	'progress_content' => 'string',
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
    public function getDataById($id, $fields = array()) {

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
        if (isset($params['progress_id'])) {
            if (is_array($params['progress_id'])) {
                $ret->whereIn('progress_id', $params['progress_id']);
            }else{
                $ret->where('progress_id', '=', $params['progress_id']);
            }
        }
        if (isset($params['task_id'])) {
            if (is_array($params['task_id'])) {
                $ret->whereIn('task_id', $params['task_id']);
            }else{
                $ret->where('task_id', '=', $params['task_id']);
            }
        }
        if (isset($params['process_id'])) {
            if (is_array($params['process_id'])) {
                $ret->whereIn('process_id', $params['process_id']);
            }else{
                $ret->where('process_id', '=', $params['process_id']);
            }
        }
        if (isset($params['action_type'])) {
            if (is_array($params['action_type'])) {
                $ret->whereIn('action_type', $params['action_type']);
            }else{
                $ret->where('action_type', '=', $params['action_type']);
            }
        }
        if (isset($params['current_user_id'])) {
            if (is_array($params['current_user_id'])) {
                $ret->whereIn('current_user_id', $params['current_user_id']);
            }else{
                $ret->where('current_user_id', '=', $params['current_user_id']);
            }
        }
        if (isset($params['status'])) {
            if (is_array($params['status'])) {
                $ret->whereIn('status', $params['status']);
            }else{
                $ret->where('status', '=', $params['status']);
            }
        }
        if (isset($params['progress_content'])) {
            $ret->where('progress_content', 'LIKE', '%'.$params['progress_content'].'%');
        }

        $ret->hash(self::$pk);
		$ret->orderBy(self::$pk,'ASC');
        return $ret->get();
    }

    /**
     * 添加
     */
    public static function insert($params) {
		
        if (isset($params['task_id']) && isset($params['current_user_id']) && isset($params['process_id']) && isset($params['status'])) {
            $params = array_intersect_key($params, self::$fields);
            $params = array_merge(self::$fields, $params);

            if (!isset($params['create_time']) || empty($params['create_time'])) {
                $params['create_time'] = date('Y-m-d H:i:s');
            }

            unset($params[self::$pk]);
        } else if (isset($params[0]) && is_array($params[0])) {
            foreach ($params as $k => $v) {
                if (!isset($v['task_id']) || !isset($v['current_user_id']) || !isset($v['process_id']) || !isset($v['status'])) {
                    return FALSE;
                }

                if (!isset($v['create_time']) || empty($v['create_time'])) {
                    $v['create_time'] = date('Y-m-d H:i:s');
                }

                $v = array_intersect_key($v, self::$fields);
                $v = array_merge(self::$fields, $v);
                unset($v[self::$pk]);
                $params[$k] = $v;
            }
        } else {
            return FALSE;
        }

        $qb = self::$conn->getQueryBuilder();
        $ret = $qb->table(static::$tableName);
        return $ret->insert($params);
    }


    /**
     * 更新
     */
    public static function update($params) {

        if (!isset($params[self::$pk])) {
            return FALSE;
        }

        $params = array_intersect_key($params, self::$fields);
        $pkid = $params[self::$pk];
        unset($params[self::$pk]);

        $qb = self::$conn->getQueryBuilder();
        $ret = $qb->table(static::$tableName);
        $ret = $ret->where(self::$pk, $pkid);
        return $ret->update($params);
    }
}