<?php
namespace WorkFlowAtom\Package\Tree;

/**
 * 工作流类型
 * @package Admin\Package\Workflow
 * @author yixiangwang@meilishuo.com
 * @since 2015-07-10
 */

use WorkFlowAtom\Package\Common\DbAdapter;

class TreePermissions  {

    private static $instance = null;
    private static $conn;

    private static $tableName = 'tree_role_permissions';
    private static $col = array('id', 'tree_id', 'role_id', 'parent_id', 'status');
    private static $pk = 'id';
    private static $fields = array(
        'id'                => 0,
		'tree_id'			=> 0,
		'role_id'			=> '',
		'status'			=> 0,//状态:0无效1有效
		'parent_id'         => 0
    );
    private static $update_fields = array(
		'tree_id'			=> 'string',
		'role_id'			=> 'string',
		'status'			=> 'int',
		'parent_id'	        => 'int'
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
        if (isset($params['tree_id'])) {
            if (is_array($params['tree_id'])) {
                $ret->whereIn('tree_id', $params['tree_id']);
            }else{
                $ret->where('tree_id', '=', $params['tree_id']);
            }
        }
        if (isset($params['status'])) {
            if (is_array($params['status'])) {
                $ret->whereIn('status', $params['status']);
            }else{
                $ret->where('status', '=', $params['status']);
            }
        }
        if (isset($params['role_id'])) {
            if (is_array($params['role_id'])) {
                $ret->whereIn('role_id', $params['role_id']);
            }else{
                $ret->where('role_id', '=', $params['role_id']);
            }
        }
		if (isset($params['parent_id'])) {
            if (is_array($params['parent_id'])) {
                $ret->whereIn('parent_id', $params['parent_id']);
            }else{
                $ret->where('parent_id', '=', $params['parent_id']);
            }
        }
        $ret->orderBy('id', 'asc');

        $ret->hash(self::$pk);
        return $ret->get();
    }

    /**
     * 添加
     */
    public static function insert($params) {

        if (!isset($params['role_id'])){
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
        $ret = $ret->where(self::$pk, $pkid);
        return $ret->update($params);
    }

} 
