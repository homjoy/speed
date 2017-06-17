<?php
namespace Atom\Package\Notice;

use Atom\Package\Common\BaseQuery;

/**
 *
 * Class NoticeMarked
 * @package Atom\Package\Notice
 *
 */

class NoticeMarked extends BaseQuery {

    /**
     * @return string
     */
    public static function database()
    {
        return 'core';
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        return 'notice_marked';
    }

    /**
     * @return string
     */
    public static function pk()
    {
        return 'id';
    }

    public static $col = array('id', 'notice_id', 'user_id', 'ctime', 'status', );

    /**
     * 字段列表
     * @var array
     */
    public static $fields = array(
        'id'            => 0,
        'notice_id'     => 0,
        'user_id'       => 0,
        'status'        => 1, //状态:0无效1有效
    );

    public static function getFields()
    {
        return self::$fields;
    }

    /**
     * 获取信息
     * @param $id
     * @param array $fields
     * @return array
     */
    public function getDataList($params = array(), $offset = 0, $limit = 100)
    {
        if (!is_array($params)) {
            return FALSE;
        }
        //在职状态：默认在职
        if (!isset($params['status'])) {
            $params['status'] = array(1);
        }

        //查询
        $builder = $this->builder();
        $builder->select(static::$col);

        //notice_id
        if (!empty($params['notice_id'])) {
            if (is_array($params['notice_id'])) {
                $builder->whereIn('notice_id', $params['notice_id']);
            }else{
                $builder->where('notice_id', '=', $params['notice_id']);
            }
        }

        //user_id
        if (!empty($params['user_id'])) {
            if (is_array($params['user_id'])) {
                $builder->whereIn('user_id', $params['user_id']);
            }else{
                $builder->where('user_id', '=', $params['user_id']);
            }
        }

        //ctime
        if (!empty($params['ctime'])) {
            $builder->where('ctime', '>=', $params['ctime']);
        }

        //状态
        if (is_array($params['status'])) {
            $builder->whereIn('status', $params['status']);
        }else{
            $builder->where('status', '=', $params['status']);
        }

        $builder->orderBy(static::pk(),'asc');
/*
        $builder->hash(static::pk());
        $queryObj = $builder->getQuery();
        echo $queryObj->getRawSql();
*/
        return $builder->hash(static::pk())->offset($offset)->limit($limit)->get();
    }

    /**
     * 添加
     */
    public function insert($params) {

        if (!isset($params['notice_id']) || !isset($params['user_id'])){
            return FALSE;
        }

        $params = array_intersect_key($params, self::$fields);
        $params = array_merge(self::$fields, $params);
        unset($params[static::pk()]);

        return $this->builder()->insert($params);
    }

}