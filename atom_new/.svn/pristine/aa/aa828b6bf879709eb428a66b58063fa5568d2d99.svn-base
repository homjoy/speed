<?php
namespace Atom\Package\Core;

use Atom\Package\Common\BaseQuery;


/**
 * 对修改密码时间的操作
 * @author haibinzhou
 * @date 2015-07-07
 */
class MlsAccount extends BaseQuery{

    private static $instance = NULL ;

    /**
     * 帐号类型
     */
    public static $TYPE_VPN = 1;
    public static $TYPE_REDMINE = 2;
    public static $TYPE_SVN = 3;
    public static $TYPE_OSYS = 4;
    public static $TYPE_MAIL_PWD = 5; //邮箱密码修改登记


    /**
     * 所有字段及默认值
     */
    private static $fields = array(
        'id'   => '' ,
        'user_id'      => 0 ,
        'account_type' => 0 ,
        'login_name'   => '' ,
        'update_time'  => '0000-00-00 00:00:00' ,
        'status'       => 1 ,
    );


    public static function tableName(){
        return 'user_account';
    }

    public static function pk(){
        return 'id';
    }

    public static function model(){
        return parent::model();
    }

    public static function getFields(){
        return self::$fields;
    }

    /**
     * 添加
     */
    public function addData($params = array()) {

        if(!isset($params['user_id']) || !isset($params['account_type']) || !isset($params['login_name']) || !isset($params['status'])) {
            return FALSE;
        }

        $params = array_intersect_key($params, static::$fields);
        $params = array_merge(static::$fields, $params);

        unset($params[static::pk()]);
      //  print_r($params);die;

        return $this->builder()->insert($params);

    }

    /**
     * 批量获取用户的注册情况
     */
    public function getData($params = array()){

        if(!isset($params['user_id']) && !isset($params['account_type'])){
            return False;
        }
        $builder = $this->builder();

        if(isset($params['user_id'])){
            $user_id = is_array($params['user_id']) ? $params['user_id'] : array($params['user_id']);
            $builder->whereIn('user_id',$user_id);
        }

        $account_type = isset($params['account_type']) ? $params['account_type'] : 5;

        $result = $builder->where('status',1)->where('account_type',$account_type)->get();

        return $result;

    }

    /**
     * 更新
     * @param array 必须包涵主键
     */
    public function updateDataById($params = array()) {

        if (!isset($params['id'])){
            return FALSE;
        }

        $builder = $this->builder();
        $pk = $params[static::pk()];
        return $builder->where(static::pk(),$pk)->update($params);  //成功返回1
    }

    //更具accout_type获取用户信息
    public  function getByType($account_type = '', $status = 1) {
        $account_type = intval($account_type);

        if(empty($account_type)) {
            return FALSE;
        }

        $builder = $this->builder();
        $result = $builder->where('status',1)->where('account_type',$account_type)->get();

        return $result;
    }






    /**-------------------- 下边的方法暂时没用到 haibinzhou 15-07-30 -----------**/



    /**
     * 删除
     * @param array
     */
    public static function deleteData($user_id = '', $account_type = '') {
        if (empty($user_id) || empty($account_type)){
            return FALSE;
        }

        $sqlData = array();
        $sql = 'DELETE FROM ' . self::$table . ' WHERE user_id = :_user_id and account_type = :_account_type';
        $sqlData = array(
            '_user_id'      => $user_id,
            '_account_type' => $account_type,
        );


    }



    /**
     * 修改用户的注册情况
     */
    public static function updateStatus($user_id = '', $account_type = '', $status = 2){
        if(empty($user_id)) {
            return FALSE;
        }

        $status = intval($status);
        $sql = ' UPDATE ' . self::$table . " SET status = {$status} WHERE user_id =:_user_id ";
        $params['_user_id'] = $user_id;

        $account_type = intval($account_type);
        if(!empty($account_type)) {
            $sql .= ' AND account_type =:_account_type ';
            $params['_account_type'] = $account_type;
        }

        $result = DBGateHelper::getConn()->write($sql, $params);

        return $result;
    }

    /**
     * 检测帐号是否注册
     * @param string $user_id
     * @param string $account_type
     * @return number -1参数错误 0未注册 1已注册
     */
    public static function checkRegister($user_id = '', $account_type = '') {

        $user_id = intval($user_id);
        $account_type = intval($account_type);

        if(empty($user_id) || empty($account_type)) {
            return -1;
        }

        $sql = ' SELECT ' .self::$col. ' FROM ' . self::$table . ' WHERE user_id =:_user_id AND account_type =:_account_type and status=1';
        $params = array(
            '_user_id'      => $user_id,
            '_account_type' => $account_type
        );

        $result = DBGateHelper::getConn()->read($sql, $params);

        if(!empty($result)) {
            return 1;
        }else {
            return 0;
        }
    }




}