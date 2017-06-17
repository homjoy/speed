<?php
namespace Atom\Package\Core;

use Atom\Package\Common\BaseQuery;


/**
 * 部门信息数据备份
 * @author haibinzhou@meilishuo.com
 * @date 2015-09-24
 */
class DataBackup extends BaseQuery{

    public static $col = array('id', 'table','data','title', 'update_time');

    /**
     * 所有字段及默认值
     */
    private static $fields = array(
        'id'        => '' ,
        'table'     => 0 ,
        'data'      => 0 ,
        'title'     => '' ,
    );

    public static function getFields()
    {
        return self::$fields;
    }

    /**
     * @return string
     */
    public static function database()
    {
        return 'core';
    }


    public static function tableName(){
        return 'data_backup';
    }

    public static function pk(){
        return 'id';
    }

    /**
     * 批量获取用户的注册情况
     */
    public function getDataList($params = array(), $offset = 0, $limit = 100){

        if (!is_array($params)) {
            return FALSE;
        }

        //查询
        $builder = $this->builder();
        $builder->select(static::$col);

        if (!empty($params['id'])) {
            if (is_array($params['id'])) {
                $builder->whereIn('id', $params['id']);
            }else{
                $builder->where('id', '=', $params['id']);
            }
        }

        //table
        if (!empty($params['table'])) {
            switch ($params['match']) {
                case 'like':
                    $builder->where('table', 'LIKE', '%'.$params['table'].'%');
                    break;
                case '=':
                    $builder->where('table', '=', $params['table']);
                    break;
                default:
                    $builder->where('table', '=', $params['table']);
                    break;
            }
        }

        if (!empty($params['type'])) {
            if (is_array($params['type'])) {
                $builder->whereIn('type', $params['type']);
            }else{
                $builder->where('type', '=', $params['type']);
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

        $builder->orderBy(static::pk(),'desc');

        //是否获取全部
        if(isset($params['all']) && $params['all'] == 1){
            return $builder->hash(static::pk())->get();
        }
        if(isset($params['count']) && $params['count'] == 1){
            return $builder->count();
        }

        return $builder->hash('id')->offset($offset)->limit($limit)->get();
    }

    /**
     * 更新
     * @param array 需要用主键
     */
    public function updateDataById($params = array()) {

        if (!isset($params['id'])){
            return FALSE;
        }

        if(isset($params['id'])  && $params['id'] > 0) {
            $id = intval($params['id']);
            unset($params['id']);
            return $this->builder()
                ->where('id',$id)->update($params);
        }

        return FALSE;
    }

    public function insert($params) {
        if (empty($params['table']) || empty($params['data'])){
            return FALSE;
        }

        $params = array_intersect_key($params, self::$fields);
        $params = array_merge(self::$fields, $params);
        unset($params[static::pk()]);

        return $this->builder()->insert($params);
    }

}