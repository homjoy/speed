<?php
namespace Atom\Package\Account;

use Atom\Package\Common\BaseQuery;
use Atom\Package\Helper\StaffDBHelper;
use Libs\Util\ArrayUtilities;

/**
 *
 * Class DepartmentInfo
 * @package Atom\Package\Account
 *
 */

class DepartmentInfo extends BaseQuery {

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
        return 'department_info';
    }

    /**
     * @return string
     */
    public static function pk()
    {
        return 'depart_id';
    }

    public static $col = array('depart_id', 'depart_name', 'depart_info', 'depart_level', 'parent_id', 'child_id', 'memo', 'update_time', 'is_official', 'is_virtual', 'level', 'status',);

    /**
     * 字段列表
     * @var array
     */
    public static $fields = array(
        'depart_id'     => 0,
        'depart_name'   => '',
        'depart_info'   => '',
        'depart_level'  => 0,
        'parent_id'     => 0,
        'child_id'      => '',
        'memo'          => '',
        //'update_time'   => '0000-00-00',
        'is_official'   => 0,
        'is_virtual'    => 0,
        'level'         => 0,
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

        //是否正式部门：默认所有部门
        if (!isset($params['is_official'])) {
            $params['is_official'] = array(0,1);
        }
        //是否虚拟部门：默认所有部门
        if (!isset($params['is_virtual'])) {
            $params['is_virtual'] = array(0,1);
        }
        //匹配方式：默认严格匹配
        if (isset($params['match'])) {
            switch ($params['match']) {
                case 'like':
                    $params['match'] = 'LIKE';
                    break;
                case 'equal':
                default:
                    $params['match'] = '=';
                    break;
            }
        }else{
            $params['match'] = '=';
        }

        //查询
        $builder = $this->builder();
        $builder->select(static::$col);

        //depart_id
        if (!empty($params['depart_id'])) {
            if (is_array($params['depart_id'])) {
                $builder->whereIn('depart_id', $params['depart_id']);
            }else{
                $builder->where('depart_id', '=', $params['depart_id']);
            }
        }

        //parent_id
        if (!empty($params['parent_id'])) {
            if (is_array($params['parent_id'])) {
                $builder->whereIn('parent_id', $params['parent_id']);
            }else{
                $builder->where('parent_id', '=', $params['parent_id']);
            }
        }

        //depart_name
        if (!empty($params['depart_name'])) {
            switch ($params['match']) {
                case 'LIKE':
                    $builder->where('depart_name', 'LIKE', '%'.$params['depart_name'].'%');
                    break;
                case '=':
                    $builder->where('depart_name', '=', $params['depart_name']);
                    break;
                default:
                    $builder->where('depart_name', '=', $params['depart_name']);
                    break;
            }
        }

        //时间判定
        if(isset($params['update_time'])|| isset($params['end_time'])){
            $builder->where(function($builder)use($params){
                if (isset($params['update_time'])  && isset($params['end_time'])){
                    $builder->whereBetween('update_time',$params['update_time'],$params['end_time']);
                }  elseif (isset($params['end_time'])){
                    $builder->where('update_time','<=', $params['end_time']);
                }  elseif(isset($params['update_time'])) {
                    $builder->where('update_time','>=', $params['update_time']);
                }
            });
        }
        //时间判定
        if(isset($params['update_time'])|| isset($params['end_time'])){
            $builder->where(function($builder)use($params){
                if (isset($params['update_time'])  && isset($params['end_time'])){
                    $builder->whereBetween('update_time',$params['update_time'],$params['end_time']);
                }  elseif (isset($params['end_time'])){
                    $builder->where('update_time','<=', $params['end_time']);
                }  elseif(isset($params['update_time'])) {
                    $builder->where('update_time','>=', $params['update_time']);
                }
            });
        }
        //状态
        if (is_array($params['status'])) {
            $builder->whereIn('status', $params['status']);
        }else{
            $builder->where('status', '=', $params['status']);
        }
        //层级
        if (isset($params['depart_level'])) {
            if (is_array($params['depart_level'])) {
                $builder->whereIn('depart_level', $params['depart_level']);
            }else{
                $builder->where('depart_level', '=', $params['depart_level']);
            }
        }

        $builder->orderBy(static::pk(),'asc')->get();

/*
        $builder->hash(static::pk());
        $queryObj = $builder->getQuery();
        echo $queryObj->getRawSql();
*/
        //是否获取全部
        if(isset($params['all']) && $params['all'] == 1){
          return $builder->hash(static::pk())->get();
        }
        if(isset($params['count']) && $params['count'] == 1){
            return $builder->count();
        }
        return $builder->hash(static::pk())->offset($offset)->limit($limit)->get();
    }
    /**
     * 更新
     */
    public function  updateById($params){
        $pk = static::pk();
        if(isset($params[$pk]) && $params[$pk] > 0){
            $id = intval($params[$pk]);
            unset($params[$pk]);
            return $this->builder()
                ->where($pk,$id)->update($params);
        }
         return FALSE;
    }
    /**
     * 添加
     */
    public function insert($params) {
       //部门名字
        if ( !isset($params['depart_name'])){
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
    public function insertAll() {
        $sql = 'insert into ' . self::tableName() . ' select * from department_info_temp';

        return StaffDBHelper::getConn()->write($sql, array());
    }

    /**
     * 删除全部（谨慎使用）
     */
    public function deleteAll(){
        $sql = 'delete from ' . self::tableName();

        return StaffDBHelper::getConn()->write($sql, array());
    }

}
