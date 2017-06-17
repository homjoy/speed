<?php
namespace Atom\Package\Api;

use Atom\Package\Common\BaseQuery;

/**
 *
 * Class Otpauth
 * @package Atom\Package\Api
 *
 */

class Otpauth extends BaseQuery {

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
        return 'user_otpauth';
    }

    /**
     * @return string
     */
    public static function pk()
    {
        return 'id';
    }

    public static $col = array('id', 'user_id', 'ip', 'secret', 'expire_time', 'ctime', 'status',);

    /**
     * 字段列表
     * @var array
     */
    public static $fields = array(
        'id'            => 0,
        'user_id'       => 0,
        'ip'            => 0,
        'secret'        => '',
        'expire_time'   => 0,
        //'ctime'         => '0000-00-00',
        'status'        => 1, //0无效 1生成 2验证通过上线
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
            $params['status'] = array(0,1,2);
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

        //secret
        if (!empty($params['secret'])) {
            if (is_array($params['secret'])) {
                $builder->whereIn('secret', $params['secret']);
            }else{
                $builder->where('secret', '=', $params['secret']);
            }
        }

        //expire_time
        if (!empty($params['expire_time'])) {
            $builder->where('expire_time', '>=', $params['expire_time']);
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

        if (!isset($params['secret'])){
            return FALSE;
        }

        $params = array_intersect_key($params, self::$fields);
        $params = array_merge(self::$fields, $params);
        unset($params[static::pk()]);

        return $this->builder()->insert($params);
    }

    /**
     * 添加
     */
    public function update($params) {

        if (!isset($params['user_id'])){
            return FALSE;
        }

        $params = array_intersect_key($params, self::$fields);
        if (isset($params[static::pk()])) {
            unset($params[static::pk()]);
        }

        return $this->builder()->where('user_id',$params['user_id'])->update($params);
    }

}