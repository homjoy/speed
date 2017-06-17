<?php
namespace Atom\Package\Account;

use Atom\Package\Common\BaseQuery;

/**
 *
 * Class UserJobTitle
 * @package Atom\Package\Account
 * @author haibinzhou@meilishuo.com
 * @date 2015-08-24
 *
 */

class UserJobTitle extends BaseQuery {

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
        return 'user_job_title';
    }

    /**
     * @return string
     */
    public static function pk()
    {
        return 'title_id';
    }

    public static $col = array('title_id', 'depart_id', 'title_name', 'title_info', 'memo', 'update_time', 'status');

    /**
     * 字段列表
     * @var array
     */
    public static $fields = array(
        'title_id'     => 0,
        'depart_id'    => '',
        'title_name'   => '',
        'title_info'   => 0,
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

        //title_id
        if (!empty($params['title_id'])) {
            if (is_array($params['title_id'])) {
                $builder->whereIn('title_id', $params['title_id']);
            }else{
                $builder->where('title_id', '=', $params['title_id']);
            }
        }

        //parent_id
        if (!empty($params['depart_id'])) {
            if (is_array($params['depart_id'])) {
                $builder->whereIn('depart_id', $params['depart_id']);
            }else{
                $builder->where('depart_id', '=', $params['depart_id']);
            }
        }

        //depart_name
        $params['match'] = isset($params['match'])?$params['match']:'=';
        if (!empty($params['title_name'])) {
            switch ($params['match']) {
                case 'LIKE':
                    $builder->where('title_name', 'LIKE', '%'.$params['title_name'].'%');
                    break;
                case '=':
                    $builder->where('title_name', '=', $params['title_name']);
                    break;
                default:
                    $builder->where('title_name', '=', $params['title_name']);
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
        // 判断条件depart_id title_name必须存在
        if (empty($params['depart_id']) || empty($params['title_name'])){
            return FALSE;
        }
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