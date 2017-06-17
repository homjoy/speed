<?php
namespace Atom\Package\Core;

use Atom\Package\Common\BaseQuery;

/**
 * Class UserToken
 * @package Atom\Package\Core
 */
class UserToken extends BaseQuery{

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
        return 'user_token';
    }

    /**
     * @return string
     */
    public static function pk()
    {
        return 'id';
    }

    public static $col = array('id', 'user_id', 'type', 'token', 'ctime', );

	private static $fields = array(
		'id'		=> 0,
		'user_id'	=> 0,
		'type'		=> '',
		'token'		=> '',
		'ctime'	    => '0000-00-00 00:00:00',
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

        //查询
        $builder = $this->builder();
        $builder->select(static::$col);

        //token
        if (!empty($params['token'])) {
            if (is_array($params['token'])) {
                $builder->whereIn('token', $params['token']);
            }else{
                $builder->where('token', '=', $params['token']);
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

        //type
        if (!empty($params['type'])) {
	        if (is_array($params['type'])) {
	            $builder->whereIn('type', $params['type']);
	        }else{
	            $builder->where('type', '=', $params['type']);
	        }
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
        $ret = $qb->get();
        return $ret;
    }

	/**
	 * 根据Id查询
     * @param int $id
     * @return array
	 */
    public function getDataById($id){
		if(is_array($id)){
			$res = $this->builder()->whereIn(self::$pk,$id)->get();
		}else{
			$res = $this->builder()->where(self::$pk,'=',$id)->get();
		}

		return $res;
    }

}
