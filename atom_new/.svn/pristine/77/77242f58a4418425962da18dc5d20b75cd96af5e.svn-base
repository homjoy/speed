<?php
namespace Atom\Package\Notice;

use Atom\Package\Common\BaseQuery;

/**
 *
 * Class NoticeInfo
 * @package Atom\Package\Notice
 *
 */

class NoticeInfo extends BaseQuery {

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
        return 'notice_info';
    }

    /**
     * @return string
     */
    public static function pk()
    {
        return 'notice_id';
    }

    public static $col = array('notice_id', 'type', 'user_id', 'style', 'title', 'content', 'link', 'is_always', 'start_time', 'end_time', 'ctime', 'status', 'icon', 'new_window');

    /**
     * 字段列表
     * @var array
     */
    public static $fields = array(
        'notice_id'     => 0,
        'type'          => 0, //类型 1:success;2:warning;3:danger
        'user_id'       => 0,
        'style'         => '',
        'title'         => '',
        'content'       => '',
        'link'          => '',
        'is_always'     => 0,
        'start_time'    => '0000-00-00',
        'end_time'      => '0000-00-00',
        'status'        => 1, //状态:0无效1有效
        'icon'          => '', //图标（laba）
        'new_window'    => 1, //1,新窗口0,当前页
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

        //type
        if (!empty($params['type'])) {
            if (is_array($params['type'])) {
                $builder->whereIn('type', $params['type']);
            }else{
                $builder->where('type', '=', $params['type']);
            }
        }

        //is_always
        if (!empty($params['is_always'])) {
            if (is_array($params['is_always'])) {
                $builder->whereIn('is_always', $params['is_always']);
            }else{
                $builder->where('is_always', '=', $params['is_always']);
            }
        }

        //title
        if (!empty($params['title'])) {
            $builder->where('title', 'LIKE', '%'.$params['title'].'%');
        }

        //start_time
        if (!empty($params['start_time'])) {
            $builder->where('start_time', '>=', $params['start_time']);
        }

        //end_time
        if (!empty($params['end_time'])) {
            $builder->where('end_time', '<=', $params['end_time']);
        }

        //in_time
        if (!empty($params['in_time'])) {
            $builder->where('start_time', '<=', $params['in_time']);
            $builder->where('end_time', '>=', $params['in_time']);
        }
        //admin_end_time
        if (!empty($params['admin_end_time'])) {
            $builder->where('start_time', '<=', $params['admin_end_time']);
        }

        //admin_start_time
        if (!empty($params['admin_start_time'])) {
            $builder->where('end_time', '>=', $params['admin_start_time']);
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
        //获取总页数
        if(isset($params['count']) && $params['count'] == 1){
            return $builder->count();
        }
        return $builder->hash(static::pk())->offset($offset)->limit($limit)->get();
    }

    /**
     * 添加
     */
    public function insert($params) {

        if (!isset($params['type']) || !isset($params['start_time']) || !isset($params['end_time'])){
            return FALSE;
        }

        $params = array_intersect_key($params, self::$fields);
        $params = array_merge(self::$fields, $params);
        unset($params[static::pk()]);

        return $this->builder()->insert($params);
    }

}