<?php
namespace Atom\Package\Account;

use Atom\Package\Common\BaseQuery;

/**
 *
 * Class DepartmentLeader
 * @package Atom\Package\Account
 *
 */

class DepartmentLeaderInfo extends BaseQuery {

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
        return 'department_leader';
    }

    /**
     * @return string
     */
    public static function pk()
    {
        return 'leader_id';
    }

    public static $col = array('leader_id', 'depart_id', 'user_id', 'update_time', 'status');

    /**
     * 字段列表
     * @var array
     */
    public static $fields = array(
        'leader_id'      => 0,
        'depart_id'      => 0,
        'user_id'        => 0,
       // 'update_time'    => '0000-00-00',
        'status'         => 1,
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

        if (!empty($params['depart_id'])) {
            if (is_array($params['depart_id'])) {
                $builder->whereIn('depart_id', $params['depart_id']);
            }else{
                $builder->where('depart_id', '=', $params['depart_id']);
            }
        }

        if (!empty($params['leader_id'])) {
            if (is_array($params['leader_id'])) {
                $builder->whereIn('leader_id', $params['leader_id']);
            }else{
                $builder->where('leader_id', '=', $params['leader_id']);
            }
        }

        if (!empty($params['user_id'])) {
            if (is_array($params['user_id'])) {
                $builder->whereIn('user_id', $params['user_id']);
            }else{
                $builder->where('user_id', '=', $params['user_id']);
            }
        }

        //时间判定
        if (!empty($params['update_time'])  && !empty($params['end_time'])){
            $builder->whereBetween('update_time',$params['update_time'],$params['end_time']);
        }  elseif (!empty($params['end_time'])){
            $builder->where('update_time','<=', $params['end_time']);
        }  elseif(!empty($params['update_time'])){
            $builder->where('update_time','>=', $params['update_time']);
        }

        //状态
        if (is_array($params['status'])) {
            $builder->whereIn('status', $params['status']);
        }else{
            $builder->where('status', '=', $params['status']);
        }

        $builder->orderBy(static::pk(),'asc');

        $offset = $offset < 1 ? 1: intval($offset);
        return $builder->hash(static::pk())->offset(($offset-1)*$limit)->limit($limit)->get();
    }

     //更新
    public function  updateById($params){
        $pk = static::pk();
        if(isset($params[$pk])&& $params[$pk] > 0){
            $id = intval($params[$pk]);
            unset($params[$pk]);
            return $this->builder()
                ->where($pk,$id)->update($params);
        }
        if(isset($params['depart_id'])  && $params['depart_id'] > 0) {
            $id = intval($params['depart_id']);
            unset($params['depart_id']);
            return $this->builder()
                ->where('depart_id',$id)->update($params);
        }
         return FALSE;
    }
     public function insert($params) { //考虑到部门必须有人管 user_id 不可为空

        if (empty($params['depart_id']) || empty($params['user_id'])){
            return FALSE;
        }

        $params = array_intersect_key($params, self::$fields);
        $params = array_merge(self::$fields, $params);
        unset($params[static::pk()]);

        return $this->builder()->insert($params);
    }
}
