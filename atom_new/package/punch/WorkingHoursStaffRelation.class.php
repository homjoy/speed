<?php

namespace Atom\Package\Punch;

use Atom\Package\Common\BaseQuery;

/**
 * 员工工作时间相关数据
 * Class CreatePunchLog
 * @package Atom\Package\Punch
 * @author guojiezhu@meilishuo.com
 * @since 2015-10-19
 */
class WorkingHoursStaffRelation extends BaseQuery {

    /**
     * @return string
     */
    public static function database() {
        return 'administration';
    }

    /**
     * @return string
     */
    public static function tableName() {
        return 'working_hours_staff_relation';
    }

    /**
     * @return string
     */
    public static function pk() {
        return 'id';
    }

    public static $col = array(
        'id',
        'user_id',
        'work_id',
        'staff_id',
        'name_cn',
        'update_time',
        'status',
        'depart_id'
    );

    /**
     * 字段列表
     * @var array
     */
    public static $fields = array(
        'id' => 0,
        'user_id' => 0,
        'work_id' => 0,
        'staff_id' => '',
        'name_cn' => '',
        'update_time' => '',
        'status' => 1,
        'depart_id' => 1,
    );

    public static function getFields() {
        return self::$fields;
    }

    /**
     * 获取信息
     * @param $id
     * @param array $fields
     * @return array
     */
    public function getDataList($params = array(), $offset = 0, $limit = 100) {

        if (!is_array($params)) {
            return FALSE;
        }

        //在职状态：默认在职
        if (!isset($params['status'])) {
            $params['status'] = array(1, 3);
        }

        //查询
        $builder = $this->builder();
        $builder->select(static::$col);
        //id
        if (!empty($params['id'])) {
            if (is_array($params['id'])) {
                $builder->whereIn('id', $params['id']);
            } else {
                $builder->where('id', '=', $params['id']);
            }
        }
        //user_id
        if (!empty($params['user_id'])) {
            if (is_array($params['user_id'])) {
                $builder->whereIn('user_id', $params['user_id']);
            } else {
                $builder->where('user_id', '=', $params['user_id']);
            }
        }

        //depart_id
        if (!empty($params['worker_id'])) {
            if (is_array($params['worker_id'])) {
                $builder->whereIn('worker_id', $params['worker_id']);
            } else {
                $builder->where('worker_id', '=', $params['worker_id']);
            }
        }
        //name_cn
        if (!empty($params['name_cn'])) {
            $builder->where('name_cn', 'LIKE', '%' . $params['name_cn'] . '%');
        }

        //staff_id
        if (!empty($params['staff_id'])) {
            $builder->where('staff_id', 'LIKE', '%' . $params['staff_id'] . '%');
        }
        if (!empty($params['update_time']) || !empty($params['end_time'])) {
            $builder->where(function($builder)use($params) {
                if (!empty($params['update_time']) && isset($params['end_time'])) {
                    $builder->whereBetween('update_time', $params['update_time'], $params['end_time']);
                } elseif (!empty($params['end_time'])) {
                    $builder->where('update_time', '<=', $params['end_time']);
                } elseif (!empty($params['update_time'])) {
                    $builder->where('update_time', '>=', $params['update_time']);
                }
            });
        }
        //状态
        if (is_array($params['status'])) {
            $builder->whereIn('status', $params['status']);
        } else {
            $builder->where('status', '=', $params['status']);
        }


        $builder->orderBy(static::pk(), 'asc');

        if (isset($params['count']) && $params['count'] == 1) {
            return $builder->count();
        }
        if (isset($params['all']) && $params['all'] == 1) {
            return $builder->hash(static::pk())->get();
        }


        return $builder->hash(static::pk())->offset($offset)->limit($limit)->get();
    }

    public function updateById($params) {
        $pk = static::pk();
        if (isset($params[$pk]) && $params[$pk] > 0) {
            $id = intval($params[$pk]);
            unset($params[$pk]);
            return $this->builder()->where($pk, $id)->update($params);
        }

        return FALSE;
    }

     /**
     * 添加
     */
    public function insert($params) {
       //名字和邮箱
        if (empty($params['name_cn'])){
            return FALSE;
        }

        $params = array_intersect_key($params, self::$fields);
        $params = array_merge(self::$fields, $params);

        unset($params[static::pk()]);

        return $this->builder()->insert($params);
    }
}
