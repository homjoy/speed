<?php

namespace Atom\Package\Mail;


use Atom\Package\Common\BaseQuery;

/**
 *
 * Class MailGroupDepartmentRelation
 * @package namespace Atom\Package\Mail;
 * @author meilishuo@meilishuo.com
 * @date 2016-03-16 11:06:08
 */
class MailGroupDepartmentRelation extends BaseQuery
{

    /**
     * @return string
     */
    public static function database()
    {
        return 'administration';
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        return 'mail_group_department_relation';
    }

    /**
     * @return string
     */
    public static function pk()
    {
        return 'id';
    }

    public static $col = array('id', 'group_id', 'depart_id', 'status');

    /**
     * 字段列表
     * @var array
     */
    public static $fields = array(
        'id' => 0,
        'group_id' => 0,
        'depart_id' => 0,
        'status' => 1,
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
        //查询
        $builder = $this->builder();
        $builder->select(static::$col);

        // id
        if (isset($params['id'])) {
            if (is_array($params['id'])) {
                $builder->whereIn('id', $params['id']);
            } else {
                $builder->where('id', '=', $params['id']);
            }

        }
// group_id
        if (isset($params['group_id'])) {
            if (is_array($params['group_id'])) {
                $builder->whereIn('group_id', $params['group_id']);
            } else {
                $builder->where('group_id', '=', $params['group_id']);
            }

        }
// depart_id
        if (isset($params['depart_id'])) {
            if (is_array($params['depart_id'])) {
                $builder->whereIn('depart_id', $params['depart_id']);
            } else {
                $builder->where('depart_id', '=', $params['depart_id']);
            }

        }
// status
        if (isset($params['status'])) {
            if (is_array($params['status'])) {
                $builder->whereIn('status', $params['status']);
            } else {
                $builder->where('status', '=', $params['status']);
            }

        }
//        $builder->hash(static::pk());
//        $queryObj = $builder->getQuery();
//        return  $queryObj->getRawSql();

        $builder->orderBy(static::pk(), 'asc');

        if (isset($params['count']) && $params['count'] == 1) {
            return $builder->count();
        }
        if (isset($params['all']) && $params['all'] == 1) {
            return $builder->hash(static::pk())->get();
        }
        return $builder->hash(static::pk())->offset($offset)->limit($limit)->get();
    }

    //更新数据
    public function updateById($params)
    {
        $pk = static::pk();
        if (isset($params[$pk]) && $params[$pk] > 0) {
            $id = intval($params[$pk]);
            unset($params[$pk]);
            return $this->builder()
                ->where($pk, $id)->update($params);
        }

        return FALSE;
    }

    //添加数据
    public function insert($params)
    {
        if (empty($params)) {
            return FALSE;
        }
        $params = array_intersect_key($params, self::$fields);
        $params = array_merge(self::$fields, $params);
        unset($params[static::pk()]);
        return $this->builder()->insert($params);
    }

    //删除 可扩充
    public function deleteById($id)
    {
        if (isset($id) && $id > 0) {
            return $this->builder()->deleteById($id);
        }

        return FALSE;
    }

}