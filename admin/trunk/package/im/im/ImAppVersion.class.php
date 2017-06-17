<?php
namespace Admin\Package\Im;

/**
 * im
 * @package Admin\Package\Im
 * @author anqiliu@meilishuo.com
 * @since 2015-11-26
 */

use Admin\Package\Common\DbAdapter;

class ImAppVersion{

    private static $instance = null;
    private static $conn;

    private static $tableName = 't_im_app_version';
    private static $col = array('id', 'v_type', 'v_code', 'v_name', 'v_md5', 'v_url', );
    private static $pk = 'id';
    private static $fields = array(
        'v_type'        => 0,
        'v_code'        => 0,
        'v_name'        => '',
        'v_md5' => 0,
        'v_url' => 0,
    );
    private static $update_fields = array(
    //  'process_name'      => 'string',
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

        return $ret->count();
      }


    /**
     * 查询列表
     */
    public function getDataList(array $params = array(),$offset=0, $limit = 20)
    {

        //查询
        $qb = self::$conn->getQueryBuilder();
        $ret = $qb->table(static::$tableName)
            ->select(static::$col);
            //->offset(($page-1)*$pageSize)
            //->limit($pageSize);

        if (isset($params['v_type'])) {
            if (is_array($params['v_type'])) {
                $ret->whereIn('v_type', $params['v_type']);
            }else{
                $ret->where('v_type', '=', $params['v_type']);
            }
        }

        $ret->offset($offset)
            ->limit($pageSize);

        $ret->hash(self::$pk);
        $ret->orderBy(self::$pk,'DESC');
        return $ret->get();
    }

    /**
     * 添加
     */
    public static function insert($params) {

        if (!isset($params['v_type']) || !isset($params['v_code'])){
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