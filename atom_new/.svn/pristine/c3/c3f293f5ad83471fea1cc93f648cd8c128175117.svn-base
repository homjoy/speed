<?php
namespace Atom\Package\Account;

use Atom\Package\Common\BaseQuery;

/**
 *
 * Class UserAvatar
 * @package Atom\Package\Account
 *
 */

class UserAvatar extends BaseQuery {

    /**
     * @return string
     */
    public static function database()
    {
        return 'staff';
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        return 'user_avatar';
    }

    /**
     * @return string
     */
    public static function pk()
    {
        return 'id';
    }

    public static $col = array('id', 'user_id', 'avatar_src', 'avatar_big', 'avatar_middle', 'avatar_small', 'local_src', 'local_big', 'local_middle', 'local_small', 'update_time', 'status',);

    public static $fields = array(
        'id'            => 0,
        'user_id'       => 0,
        'avatar_src'    => '',
        'avatar_big'    => '',
        'avatar_middle' => '',
        'avatar_small'  => '',
        'local_src'     => '',
        'local_big'     => '',
        'local_middle'  => '',
        'local_small'   => '',
        'status'        => 1,
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
        //状态：默认有效数据
        if (!isset($params['status'])) {
            $params['status'] = array(1);
        }

        //查询
        $builder = $this->builder();
        $builder->select(static::$col);

        //id
        if (!empty($params['id'])) {
            if (is_array($params['id'])) {
                $builder->whereIn('id', $params['id']);
            }else{
                $builder->where('id', '=', $params['id']);
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

        //update_time
        if (!empty($params['update_time'])) {
            $builder->where('update_time', '>=', $params['update_time']);
        }

        //状态
        if (is_array($params['status'])) {
            $builder->whereIn('status', $params['status']);
        }else{
            $builder->where('status', '=', $params['status']);
        }
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

        if (!isset($params['user_id']) || !isset($params['avatar_src'])){
            return FALSE;
        }

        $params = array_intersect_key($params, self::$fields);
        $params = array_merge(self::$fields, $params);
        unset($params[static::pk()]);

        return $this->builder()->insert($params);
    }

}