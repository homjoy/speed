<?php
namespace Atom\Package\Account;

use Atom\Package\Common\BaseQuery;

/**
 *
 * Class UserJobLevel
 * @package Atom\Package\Account
 * @author haibinzhou@meilishuo.com
 * @date 2015-08-24
 *
 */

class UserJobLevel extends BaseQuery {

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
        return 'user_job_level';
    }

    /**
     * @return string
     */
    public static function pk()
    {
        return 'level_id';
    }

    public static $col = array('level_id', 'level_name', 'level_info', 'memo', 'update_time', 'status');

    /**
     * 字段列表
     * @var array
     */
    public static $fields = array(
        'level_id'      => 0,
        'level_name'    => '',
        'level_info'    => '',
        'memo'          => '',
        //'update_time'   => '0000-00-00 00:00:00',
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
        //状态：默认有效
        if (!isset($params['status'])) {
            $params['status'] = array(1);
        }

        //查询
        $builder = $this->builder();
        $builder->select(static::$col);

        //level_id
        if (!empty($params['level_id'])) {
            if (is_array($params['level_id'])) {
                $builder->whereIn('level_id', $params['level_id']);
            }else{
                $builder->where('level_id', '=', $params['level_id']);
            }
        }

        //level_name
        $params['match'] = isset($params['match'])?$params['match']:'=';
        if (!empty($params['level_name'])) {
            switch ($params['match']) {
                case 'LIKE':
                    $builder->where('level_name', 'LIKE', '%'.$params['level_name'].'%');
                    break;
                case '=':
                    $builder->where('level_name', '=', $params['level_name']);
                    break;
                default:
                    $builder->where('level_name', '=', $params['level_name']);
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