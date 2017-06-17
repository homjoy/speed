<?php
namespace Atom\Package\Crab;

use Atom\Package\Common\BaseQuery;
use Atom\Package\Migrate\Crab;
/**
 * Class CrabUsertime
 * @package Atom\Package\crab
 */
class CrabUserTime extends BaseQuery{

	private static $pk = 'time_id';

	private static $fields = array(
		'time_id'       => 0,
		'user_id'       => 0,
		'start_time'    => '0000-00-00 00:00:00',
		'end_time'      => '0000-00-00 00:00:00',
		'create_time'   => '0000-00-00 00:00:00',
		'color'         => '',
		'dowath'        => '',
		'status'        => 1,
	);

	/**
	 * 数据库表名
	 * @return string
	 */
	public static function tableName(){
		return 't_crab_time_manage';
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

        //TODO 根据查询参数params build sql.
        $ret = $qb->get();
        return $ret;
    }

}
