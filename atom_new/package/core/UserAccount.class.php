<?php
namespace Atom\Package\Core;

use Atom\Package\Common\BaseQuery;


/**
 * 用户注册信息
 * @author haibinzhou
 * @date 2015-09-07
 */
class UserAccount extends BaseQuery{

    /**
     * 帐号类型
     */
    public static $TYPE_VPN = 1;
    public static $TYPE_REDMINE = 2;
    public static $TYPE_SVN = 3;
    public static $TYPE_OSYS = 4;
    public static $TYPE_MAIL_PWD = 5; //邮箱密码修改登记

    public static $col = array('id', 'user_id','account_type','login_name', 'update_time', 'status');

    /**
     * 所有字段及默认值
     */
    private static $fields = array(
        'id'   => '' ,
        'user_id'      => 0 ,
        'account_type' => 0 ,
        'login_name'   => '' ,
        'status'       => 1 ,
        'update_time'  => '0000-00-00 00:00:00' ,
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
        return 'user_account';
    }

    public static function pk(){
        return 'id';
    }

    /**
     * 批量获取用户的注册情况
     */
    public function getData($params = array(), $offset = 0, $limit = 100){

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

        if (!empty($params['id'])) {
            if (is_array($params['id'])) {
                $builder->whereIn('id', $params['id']);
            }else{
                $builder->where('id', '=', $params['id']);
            }
        }

        //status  hongzhou@meilishuo.com  添加
        if (!empty($params['status'])) {
            if (is_array($params['status'])) {
                $builder->whereIn('status', $params['status']);
            }else{
                $builder->where('status', '=', $params['status']);
            }
        }


        if (!empty($params['user_id'])) {
            if (is_array($params['user_id'])) {
                $builder->whereIn('user_id', $params['user_id']);
            }else{
                $builder->where('user_id', '=', $params['user_id']);
            }
        }

        if(isset($params['account_type'])){
            if (is_array($params['account_type'])) {
                $builder->whereIn('account_type', $params['account_type']);
            }else{
                $builder->where('account_type', '=', $params['account_type']);
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

        $builder->orderBy(static::pk(),'asc');

        if(isset($params['count']) && $params['count'] == 1){

            return $builder->count();
        }

        if(isset($params['all']) && $params['all'] == 1){
            return $builder->hash(static::pk())->get();
        }

        return $builder->hash('user_id')->offset($offset)->limit($limit)->get();
    }

    /**
     * 更新
     * @param array 需要用主键或者用户id
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

        if(isset($params['user_id'])  && $params['user_id'] > 0) {
            $user_id = intval($params['user_id']);
            return $this->builder()
                ->where('user_id',$user_id)->update($params);
        }


        return FALSE;
    }

    public function insert($params) {
        if (empty($params['account_type']) || empty($params['user_id'])){
            return FALSE;
        }

        $params = array_intersect_key($params, self::$fields);
        $params = array_merge(self::$fields, $params);
        unset($params[static::pk()]);

        return $this->builder()->insert($params);
    }


}