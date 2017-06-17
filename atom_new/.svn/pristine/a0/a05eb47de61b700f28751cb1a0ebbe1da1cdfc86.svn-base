<?php
namespace Atom\Package\Crab;

use Atom\Package\Common\BaseQuery;

/**
 * Class CrabStaffShareInfo
 * @package Atom\Package\crab
 */
class CrabStaffShareInfo extends BaseQuery{

	private static $pk = 'user_id';

	private static $fields = array(
		'user_id'      => 0,
		'share_id'     => 0,
		'depart_id'    => '0',
		'is_read'      => '1',
		'is_write'     => '0',
	);

	/**
	 * 数据库表名
	 * @return string
	 */
	public static function tableName(){
		return 't_crab_staff_shareinfo';
	}

	public static function pk(){
		return self::$pk;
	}

	public static function getFields(){
		return self::$fields;
	}

	/**
	 * @return \Atom\Package\Crab\CrabUsertime
	 */
	public static function model(){
		return parent::model();
	}

	/**
	 * 查询
     * @param $data
	 * @param array $params
	 * @param int $page
	 * @param int $pageSize
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

}
