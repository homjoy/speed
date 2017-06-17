<?php
namespace Atom\Package\Account;

use Atom\Package\Common\BaseQuery;

/**
 *
 * Class UserWorkInfo
 * @package Atom\Package\Account
 *
 */

class UserWorkInfo extends BaseQuery {

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
        return 'user_work_info';
    }

    /**
     * @return string
     */
    public static function pk()
    {
        return 'id';
    }

    public static $col = array('id', 'user_id', 'job_level_id', 'job_title_id', 'position', 'redmineid', 'mls_id', 'mls_nickname', 'bank_card_number', 'contract_company_id', 'business_company_id', 'others','work_city' );

    /**
     * 字段列表
     * @var array
     */
    public static $fields = array(
        'id'            => 0,
        'user_id'       => 0,
        'job_level_id'  => 0,
        'job_title_id'  => 0,
        'position'      => '',
        'redmineid'     => 0,
        'mls_id'        => 0,
        'mls_nickname'  => '',
        'bank_card_number'  => '',
        'contract_company_id'   => 0,
        'business_company_id'   => 0,
        'others'        => '',
        'work_city'     =>'',
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

        //匹配方式：默认严格匹配
        if (isset($params['match'])) {
            switch ($params['match']) {
                case 'like':
                    $params['match'] = 'LIKE';
                    break;
                case 'equal':
                    $params['match'] = '=';
                    break;
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

        //job_level_id
        if (!empty($params['job_level_id'])) {
            if (is_array($params['job_level_id'])) {
                $builder->whereIn('job_level_id', $params['job_level_id']);
            }else{
                $builder->where('job_level_id', '=', $params['job_level_id']);
            }
        }

        //job_title_id
        if (!empty($params['job_title_id'])) {
            if (is_array($params['job_title_id'])) {
                $builder->whereIn('job_title_id', $params['job_title_id']);
            }else{
                $builder->where('job_title_id', '=', $params['job_title_id']);
            }
        }

        //contract_company_id
        if (!empty($params['contract_company_id'])) {
            if (is_array($params['contract_company_id'])) {
                $builder->whereIn('contract_company_id', $params['contract_company_id']);
            }else{
                $builder->where('contract_company_id', '=', $params['contract_company_id']);
            }
        }

        //business_company_id
        if (!empty($params['business_company_id'])) {
            if (is_array($params['business_company_id'])) {
                $builder->whereIn('business_company_id', $params['business_company_id']);
            }else{
                $builder->where('business_company_id', '=', $params['business_company_id']);
            }
        }

        //mls_id
        if (!empty($params['mls_id'])) {
            if (is_array($params['mls_id'])) {
                $builder->whereIn('mls_id', $params['mls_id']);
            }else{
                $builder->where('mls_id', '=', $params['mls_id']);
            }
        }


        //position
        if (!empty($params['position'])) {
            switch ($params['match']) {
                case 'LIKE':
                    $builder->where('position', 'LIKE', '%'.$params['position'].'%');
                    break;
                case '=':
                    $builder->where('position', '=', $params['position']);
                    break;
                default:
                    $builder->where('position', '=', $params['position']);
                    break;
            }
        }
        //update_time
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
        $builder->orderBy(static::pk(),'asc');
/*
        $builder->hash(static::pk());
        $queryObj = $builder->getQuery();
        echo $queryObj->getRawSql();
*/
        //是否获取全部
        if(isset($params['all']) && $params['all'] == 1){
            return $builder->hash('user_id')->get();
        }
        //获取总页数
        if(isset($params['count']) && $params['count'] == 1){
            return $builder->count();
        }

        return $builder->hash('user_id')->offset($offset)->limit($limit)->get();
    }
    public function  updateById($params){
        //按照主键获取
        $pk = static::pk();
        if(isset($params[$pk])&& $params[$pk] > 0){
            $id = intval($params[$pk]);
            unset($params[$pk]);
            return $this->builder()
                ->where($pk,$id)->update($params);
        }
         //user_id
        if(isset($params['user_id'])  && $params['user_id'] > 0) {
            $id = intval($params['user_id']);
            unset($params['user_id']);
            return $this->builder()
                ->where('user_id',$id)->update($params);
        }
         return FALSE;
    }
    /**
     * 添加
     */
    public function insert($params) {
       //user_id
        if (!isset($params['user_id'])){
            return FALSE;
        }

        $params = array_intersect_key($params, self::$fields);
        $params = array_merge(self::$fields, $params);
        unset($params[static::pk()]);

        return $this->builder()->insert($params);
    }

}