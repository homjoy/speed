<?php
namespace WorkFlowAtom\Package\Process;

/**
 * 任务
 * @author jingjingzhang@meilishuo.com
 * @date 2015-7-20
 */

use WorkFlowAtom\Package\Common\DbAdapter;

class Task {

    private static $instance = null;
    private static $conn;

    private static $tableName = 'workflow_task';
    private static $col = array('task_id', 'tasktype_id', 'user_id', 'task_name', 'task_content', 'current_user_id', 'create_time', 'update_time', 'status', 'current_node_id');
    // private static $col = array('task_id', 'tasktype_id', 'user_id', 'task_name', 'task_content', 'current_user_id', 'create_time', 'update_time', 'status', 'current_node_id', 'current_depart_id');
    private static $pk = 'task_id';
    private static $fields = array(
            'task_id'          => 0,
            'tasktype_id'      => 0,
            'user_id'          => 0,
            'task_name'        => '',
            'task_content'     => '',
            'current_user_id'  => 0,
            'update_time'      => '',
            'status'           => 1, //1 新建；2 待接收；3 处理中；4 完成；5 挂起；6 撤销；0 失效；
            'current_node_id'  => 0, //当前节点ID
            'create_time'      => '',
            // 'current_depart_id' => 0,
    );
    private static $update_fields = array(
    	'tasktype_id'       => 'int',
        'user_id'           => 'int',
        'task_name'         => 'string',
        'task_content'      => 'string',
        'current_user_id'   => 'int',
        'status'            => 'int',
        'current_node_id'   => 'int',
        // 'current_depart_id' => 'int',
    );

    private function __construct() {}

    public static function getInstance() {
        if(is_null(self::$instance)) {
            self::$instance = new self();
            self::$conn =  new \Pixie\Connection(new DbAdapter('workflow'));
        }
        return self::$instance;
    }

    
    public static function getFields() {
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
    public function getDataList(array $params = array(), $page = 1, $pageSize = 100) {

        //查询
        $qb = self::$conn->getQueryBuilder()->table(static::$tableName);

        if (isset($params['page_flag'])) {
            $ret = $qb->select(static::$col)
                ->offset(($page-1)*$pageSize)
                ->limit($pageSize);
        } else {
            $ret = $qb->select(static::$col);
        }
       
        //查询条件
        if (isset($params['task_id'])) {
            if (is_array($params['task_id'])) {
                $ret->whereIn('task_id', $params['task_id']);
            }else{
                $ret->where('task_id', '=', $params['task_id']);
            }
        }
        if (isset($params['tasktype_id'])) {
            if (is_array($params['tasktype_id'])) {
                $ret->whereIn('tasktype_id', $params['tasktype_id']);
            }else{
                $ret->where('tasktype_id', '=', $params['tasktype_id']);
            }
        }
        if (isset($params['user_id'])) {
            if (is_array($params['user_id'])) {
                $ret->whereIn('user_id', $params['user_id']);
            }else{
                $ret->where('user_id', '=', $params['user_id']);
            }
        }
        if (isset($params['task_name'])) {
            $ret->where('task_name', 'LIKE', '%'.$params['task_name'].'%');
        }
        if (isset($params['task_content'])) {
            $ret->where('task_content', 'LIKE', '%'.$params['task_content'].'%');
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
        if (isset($params['current_node_id'])) {
            if (is_array($params['current_node_id'])) {
                $ret->whereIn('current_node_id', $params['current_node_id']);
            }else{
                $ret->where('current_node_id', '=', $params['current_node_id']);
            }
        }
        if (isset($params['start_time'])) {
            $ret->where('update_time', '>=', $params['start_time']);
        }
        if (isset($params['end_time'])) {
            $ret->where('update_time', '<=', $params['end_time']);
        }
        if (isset($params['start_create_time'])) {
            $ret->where('create_time', '>=', $params['start_create_time']);
        }
        if (isset($params['end_create_time'])) {
            $ret->where('create_time', '<=', $params['end_create_time']);
        }
        // if (isset($params['current_depart_id'])) {
        //     if (is_array($params['current_depart_id'])) {
        //         $ret->whereIn('current_depart_id', $params['current_depart_id']);
        //     }else{
        //         $ret->where('current_depart_id', '=', $params['current_depart_id']);
        //     }
        // }

        if (isset($params['count']) && $params['count'] == 1) {
            return $qb->count();
        }

        if (isset($params['order']) && !empty($params['order'])) {
            foreach ($params['order'] as $k => $v) {
                $ret->orderBy($k, $v);
            }         
        } else {
            $ret->hash(self::$pk);
            $ret->orderBy(self::$pk,'DESC');
        }
        
        return $ret->get();
    }

    /**
     * 添加
     */
    public static function insert($params) {

        if (isset($params['tasktype_id']) && isset($params['user_id']) && isset($params['task_name'])) {
            $params = array_intersect_key($params, self::$fields);
            $params = array_merge(self::$fields, $params);

            if (!isset($params['create_time']) || empty($params['create_time'])) {
                $params['create_time'] = date('Y-m-d H:i:s');
            }
            unset($params[self::$pk]);
        } else if (isset($params[0]) && is_array($params[0])) {
            foreach ($params as $k => $v) {
                if (!isset($v['tasktype_id']) || !isset($v['user_id']) || !isset($v['task_name'])) {
                    return FALSE;
                }

                $v = array_intersect_key($v, self::$fields);
                $v = array_merge(self::$fields, $v);

                if (!isset($v['create_time']) || empty($v['create_time'])) {
                    $v['create_time'] = date('Y-m-d H:i:s');
                }
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

        date_default_timezone_set("Asia/Shanghai");
        $params['update_time'] = date('Y-m-d H:i:s');

        $params = array_intersect_key($params, self::$fields);
        $pkid = $params[self::$pk];
        unset($params[self::$pk]);

        $qb = self::$conn->getQueryBuilder();
        $ret = $qb->table(static::$tableName);
        if (is_array($pkid)) {
            $ret = $ret->whereIn(self::$pk, $pkid);
        } else {
            $ret = $ret->where(self::$pk, $pkid);
        }
        
        return $ret->update($params);
    }
	
	//启动事务
	public function beginTran(){
        self::$conn->write('START TRANSACTION;');
    }
    
    public function commit(){
        self::$conn->write('COMMIT;');
    }

	public function rollback(){
        self::$conn->write('ROLLBACK;');
    }
}