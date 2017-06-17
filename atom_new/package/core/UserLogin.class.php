<?php
namespace Atom\Package\Core;

use Atom\Package\Common\BaseQuery;

/**
 * Class UserLogin
 * @package Atom\Package\Core
 */
class UserLogin extends BaseQuery{

    /**
     * @return string
     */
    public static function database()
    {
        return 'core';
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        return 'user_login';
    }

    /**
     * @return string
     */
    public static function pk()
    {
        return 'id';
    }

    public static $col = array('id', 'user_id', 'ip', 'salt', 'password', 'session', 'expire_time', 'ctime', 'status', );

	private static $fields = array(
		'id'		=> 0,
		'user_id'	=> 0,
		'ip'		=> '',
		'salt'		=> '',
		'password'  => '',
		'session'   => '',
		'expire_time'=> '',
		'ctime'     => '0000-00-00 00:00:00',
		'status'	=> 1,
	);

	public static function getFields(){
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

        //session
        if (!empty($params['session'])) {
            if (is_array($params['session'])) {
                $builder->whereIn('session', $params['session']);
            }else{
                $builder->where('session', '=', $params['session']);
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

        //ctime
        if (!empty($params['ctime'])) {
            $builder->where('ctime', '>=', $params['ctime']);
        }

        //状态
        if (is_array($params['status'])) {
            $builder->whereIn('status', $params['status']);
        }else{
            $builder->where('status', '=', $params['status']);
        }

        $builder->orderBy(static::pk(),'asc');
/*
        $builder->hash(static::pk());
        $queryObj = $builder->getQuery();
        echo $queryObj->getRawSql();
*/
        return $builder->hash(static::pk())->offset($offset)->limit($limit)->get();
    }

	/**
	 * 查询
     * @param $data
	 * @param array $params
     * @return array
	 */
    public function getList(array $params = array()){
		$qb = $this->builder();

		//查询条件
		if(!empty($params)){
			foreach($params as $k => $v){
                if(is_array($v)){
                    $qb->whereIn($k, $v);
                }else{
                    $qb->where($k,$v);
                }
			}
		}   

        //TODO 根据查询参数params build sql.
        $ret = $qb->where('status','=','1')
            ->get();
        return $ret;
    }

    /**
     * 逻辑删除数据
     * @param $params
     * @return mixed
     */
    public function deleteLogicalByUserId($params){
        if(empty($params['user_id'])){
            throw new \InvalidArgumentException(__METHOD__." wrong parameter id.");
        }
        $qb = $this->builder()->where('user_id', $params['user_id']);
        if(!empty($params['session'])){
            $qb->where('session', $params['session']);
        }
        $param = array('status' => 0);

        return $qb->update($param);

    }

}
