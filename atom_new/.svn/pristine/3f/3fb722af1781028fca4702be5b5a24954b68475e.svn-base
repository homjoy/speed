<?php
namespace Atom\Package\Account;

use Atom\Package\Common\BaseQuery;

/**
 *
 * Class UserPrivacyInfo
 * @package Atom\Package\Account
 *
 */

class UserPrivacyInfo extends BaseQuery {

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
        return 'user_privacy_info';
    }

    /**
     * @return string
     */
    public static function pk()
    {
        return 'id';
    }

    public static $col = array('id', 'user_id', 'hukou', 'education', 'school', 'speciality', 'last_work', 'emergency_person', 'emergency_phone', 'contract_start_time', 'contract_end_time', 'id_number', 'address', 'personal_mail', 'married', 'marry_time', 'children_birthday', 'update_time',);

    /**
     * 字段列表
     * @var array
     */
    public static $fields = array(
        'id'            => 0,
        'user_id'       => 0,
        'hukou'         => '',
        'education'     => '',
        'school'        => '',
        'speciality'    => '',
        'last_work'     => '',
        'emergency_person'      => '',
        'emergency_phone'       => '',
        'contract_start_time'   => '0000-00-00',
        'contract_end_time'     => '0000-00-00',
        'id_number'     => '',
        'address'       => '',
        'personal_mail' => '',
        'married'       => 0,
        'marry_time'    => '0000-00-00',
        'children_birthday'     => '0000-00-00',
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

        //user_id
        if (!empty($params['user_id'])) {
            if (is_array($params['user_id'])) {
                $builder->whereIn('user_id', $params['user_id']);
            }else{
                $builder->where('user_id', '=', $params['user_id']);
            }
        }

        //id
        if (!empty($params['id'])) {
            if (is_array($params['id'])) {
                $builder->whereIn('id', $params['id']);
            }else{
                $builder->where('id', '=', $params['id']);
            }
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
    /**
     * 更新 可以按主键和user_id 
     */    
    public function  updateById($params){
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