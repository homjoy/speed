<?php
namespace Atom\Package\Account;

use Atom\Package\Common\BaseQuery;

/**
 *
 * Class UserJobRole
 * @package Atom\Package\Account
 * @author haibinzhou@meilishuo.com
 * @date 2015-08-24
 *
 */

class UserJobRole extends BaseQuery {

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
        return 'user_job_role';
    }

    /**
     * @return string
     */
    public static function pk()
    {
        return 'role_id';
    }

    public static $col = array('role_id', 'role_name', 'role_info', 'role_level', 'memo', 'update_time', 'status');

    /**
     * 字段列表
     * @var array
     */
    public static $fields = array(
        'role_id'      => 0,
        'role_name'    => '',
        'role_info'    => '',
        'role_level'   => 0,
        'memo'         => '',
        //'update_time'  => '0000-00-00 00:00:00',
        'status'       => 1,
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
        //状态：默认有效
        if (!isset($params['status'])) {
            $params['status'] = array(1);
        }

        //查询
        $builder = $this->builder();
        $builder->select(static::$col);

        //role_id
        if (!empty($params['role_id'])) {
            if (is_array($params['role_id'])) {
                $builder->whereIn('role_id', $params['role_id']);
            }else{
                $builder->where('role_id', '=', $params['role_id']);
            }
        }

        //role_level
        if (!empty($params['role_level'])) {
            if (is_array($params['role_level'])) {
                $builder->whereIn('role_level', $params['role_level']);
            }else{
                $builder->where('role_level', '=', $params['role_level']);
            }
        }

        //role_name
        $params['match'] = isset($params['match'])?$params['match']:'=';
        if (!empty($params['role_name'])) {
            switch ($params['match']) {
                case 'LIKE':
                    $builder->where('role_name', 'LIKE', '%'.$params['role_name'].'%');
                    break;
                case '=':
                    $builder->where('role_name', '=', $params['role_name']);
                    break;
                default:
                    $builder->where('role_name', '=', $params['role_name']);
                    break;
            }
        }

        //状态
        if (is_array($params['status'])) {
            $builder->whereIn('status', $params['status']);
        }else{
            $builder->where('status', '=', $params['status']);
        }

        $builder->orderBy(static::pk(),'asc');

        //获取符合条件的总条数
        if(isset($params['count']) && $params['count'] == 1){
            return $builder->hash(static::pk())->count();
        }
        //获取符合条件的所有数据
        if(isset($params['all']) && $params['all'] == 1){
            return $builder->hash(static::pk())->get();
        }

        return $builder->hash(static::pk())->offset($offset)->limit($limit)->get();
    }

    /**
     * 添加
     */
    public function insert($params) {

        $params = array_intersect_key($params, self::$fields);
        $params = array_merge(self::$fields, $params);
        unset($params[static::pk()]);

        return $this->builder()->insert($params);
    }

    //修改
    public function  updateById($params){
        $pk = static::pk();
        if(isset($params[$pk])&& $params[$pk] > 0){
            $id = intval($params[$pk]);
            unset($params[$pk]);
            return $this->builder()
                ->where($pk,$id)->update($params);
        }
        return FALSE;
    }
}
