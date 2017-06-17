<?php
namespace Atom\Package\Core;

use Atom\Package\Common\BaseQuery;

/**
 * Class UserCaptcha
 * @package Atom\Package\Core
 */
class UserCaptcha extends BaseQuery{

    /**
     * @return string
     */
    public static function database(){
        return 'core';
    }

    /**
     * @return string
     */
    public static function tableName(){
        return 'user_captcha';
    }

    /**
     * @return string
     */
    public static function pk(){
        return 'id';
    }

    public static $col = array('id', 'user_id', 'type', 'captcha', 'expire_time', 'ctime', );

	private static $fields = array(
		'id'		    => 0,
		'user_id'	    => 0,
		'type'		    => '',
		'captcha'	    => '',
		'expire_time'	=> '0000-00-00 00:00:00',
		'ctime'	        => '0000-00-00 00:00:00',
	);

	public static function getFields(){
		return self::$fields;
	}

    /**
     * 获取信息
     * @param array $params
     * @return array
     */
    public function getList($params = array()){
        if (!is_array($params)) {
            return FALSE;
        }

        //查询
        $builder = $this->builder();
        $builder->select(static::$col);

        //captcha
        if (!empty($params['captcha'])) {
            if (is_array($params['captcha'])) {
                $builder->whereIn('captcha', $params['captcha']);
            }else{
                $builder->where('captcha', '=', $params['captcha']);
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

        //expire_time
        if (!empty($params['expire_time'])) {
            $builder->where('expire_time', '>=', $params['expire_time']);
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
        return $builder->hash(static::pk())->get();
    }

	/**
	 * 根据Id查询
     * @param int $id
     * @return array
	 */
    public function getDataById($id){
		if(is_array($id)){
			$res = $this->builder()->whereIn(static::pk(),$id)->get();
		}else{
			$res = $this->builder()->where(static::pk(),'=',$id)->get();
		}

		return $res;
    }

}
