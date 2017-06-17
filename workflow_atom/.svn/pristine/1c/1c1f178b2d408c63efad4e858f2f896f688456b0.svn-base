<?php 
namespace WorkFlowAtom\Package\User;

/**
 * 用户代理
 * @author yixiangwang@meilishuo.com
 * @since 2015-10-13
 */

use WorkFlowAtom\Package\Common\DbAdapter;

class UserAgency {

    private static $instance = null;
    private static $conn;

    private static $tableName = 'workflow_user_agency';
    private static $col = array('aid', 'o_uid', 'o_depart_id', 'a_uid', 'status');
    private static $pk = 'aid';
    private static $fields = array(
        'aid'			=> 0,
        'o_uid'			=> 0,
    	'o_depart_id'	=> 0,
    	'a_uid'			=> '',
		'status'		=> 1
    );
    private static $update_fields = array(
        'o_uid'			=> 'int',
    	'o_depart_id'	=> 'int',
    	'a_uid'			=> 'string',
		'status'		=> 'int'
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
    public function getDataList(array $params = array()) {

        //查询
        $qb = self::$conn->getQueryBuilder();
        $ret = $qb->table(static::$tableName)
            ->select(static::$col);
            //->offset(($page-1)*$pageSize)
            //->limit($pageSize);

        //查询条件
        if (isset($params['aid'])) {
            if (is_array($params['aid'])) {
                $ret->whereIn('aid', $params['aid']);
            }else{
                $ret->where('aid', '=', $params['aid']);
            }
        }
        if (isset($params['o_uid'])) {
            if (is_array($params['o_uid'])) {
                $ret->whereIn('o_uid', $params['o_uid']);
            }else{
                $ret->where('o_uid', '=', $params['o_uid']);
            }
        }
        if (isset($params['o_depart_id'])) {
            if (is_array($params['o_depart_id'])) {
                $ret->whereIn('o_depart_id', $params['o_depart_id']);
            }else{
                $ret->where('o_depart_id', '=', $params['o_depart_id']);
            }
        }
        if (isset($params['status'])) {
            if (is_array($params['status'])) {
                $ret->whereIn('status', $params['status']);
            }else{
                $ret->where('status', '=', $params['status']);
            }
        }
        
        $ret->hash(self::$pk);
        $ret->orderBy(self::$pk,'ASC');
        return $ret->get();
    }

    /**
     * 添加
     */
    public static function insert($params) {

        if ( !isset($params['o_uid']) || !isset($params['o_depart_id']) || !isset($params['a_uid']) ) {
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

    /**
     * 逻辑删除，置status为0
     */
    public static function logicDelete($ids = array()) {

        if (empty($ids)) {
            return FALSE;
        }

        $qb = self::$conn->getQueryBuilder();
        $ret = $qb->table(static::$tableName);
        $ret = $ret->whereIn(self::$pk, $ids);
        return $ret->update(array('status' => 0));
    }
}