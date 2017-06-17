<?php
namespace Atom\Package\Account;

use Atom\Package\Common\BaseQuery;

/**
 * Class UserExtendedRelation
 * @package Atom\Package\Account
 */
class UserExtendedRelation extends BaseQuery{

    /**
     * @return string
     */
    public static function database(){
        return 'staff';
    }

	private static $pk = 'id';

	private static $fields = array(
		'id'			=> 0,
		'user_id'       => 0,
		'relation_user_id' => 0,
		'type'          => 1,
        'update_time'   => '0000-00-00 00:00:00',
	);

	/**
	 * 数据库表名
	 * @return string
	 */
	public static function tableName(){
		return 'user_extended_relation';
	}

	public static function pk(){
		return self::$pk;
	}

	public static function getFields(){
		return self::$fields;
	}

	/**
	 * @return \Atom\Package\Account\UserExtendedRelation
	 */
	public static function model(){
		return parent::model();
	}

	/**
	 * 查询
     * @param $data
	 * @param array $params
     * @return array
	 */
    public function getList(array $params = array()){
		$qb = $this->builder();

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
