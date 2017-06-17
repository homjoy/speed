<?php
namespace Admin\Package\Im;

/**
 * im 
 * @package Admin\Package\Im
 * @author anqiliu@meilishuo.com
 * @since 2015-10-28
 */

use Admin\Package\Common\DbAdapter;

class ImPublicMsg{

    private static $instance = null;
    private static $conn;

    private static $tableName = 't_im_public_msg';
    private static $col = array('msg_id', 'chatfrom', 'chatto', 'msg', 'ctime', 'ext', 'source', 'type', 'msg_type', 'device');
    private static $pk = 'msg_id';
    private static $fields = array(
		'chatfrom'		=> 0,
		'chatto'		=> 0,
		'type'		=> '',
		'msg_type'	=> 0,
    );
    private static $update_fields = array(
	//	'process_name'		=> 'string',
    );

    private function __construct() {}

    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance = new self();
            self::$conn =  new \Pixie\Connection(new DbAdapter('speed_im'));
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
    * 查询总数
    */
    public function countByPararm(array $params = array()){
        $qb = self::$conn->getQueryBuilder();    
        $ret = $qb->table(static::$tableName)
            ->select(static::$col);

        if (isset($params['between_ctime'])) {
            if(!empty($params['between_ctime']['begin']) && !empty($params['between_ctime']['end']))
                $ret->where('ctime', '>=', $params['between_ctime']['begin']);
            $ret->where('ctime', '<', $params['between_ctime']['end']);
        }
        return $ret->count();
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
        if (isset($params['tasktype_id'])) {
            if (is_array($params['tasktype_id'])) {
                $ret->whereIn('tasktype_id', $params['tasktype_id']);
            }else{
                $ret->where('tasktype_id', '=', $params['tasktype_id']);
            }
        }
        if (isset($params['status'])) {
            if (is_array($params['status'])) {
                $ret->whereIn('status', $params['status']);
            }else{
                $ret->where('status', '=', $params['status']);
            }
        }
        if (isset($params['process_name'])) {
            $ret->where('process_name', 'LIKE', '%'.$params['process_name'].'%');
        }

        $ret->hash(self::$pk);
		$ret->orderBy(self::$pk,'ASC');
        return $ret->get();
    }

    /**
     * 添加
     */
    public static function insert($params) {

        if (!isset($params['process_name']) || !isset($params['tasktype_id'])){
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
