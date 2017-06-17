<?php
namespace Atom\Package\Routine;

use Atom\Package\Common\BaseQuery;

/**
 * Class UsertimeShare
 * @package Atom\Package\Routine
 */
class UserTimeShare extends BaseQuery{

	private static $pk = 'id';

    private static $fields = array(
        'id'            => 0,
        'user_id'       => 0,
        'share_id'      => 0,
        'depart_id'     => 0,
        'is_write'      => 0,
        'status'        => 1,
        'update_time'   => '0000-00-00 00:00:00',
    );

    /**
	 * 数据库表名
	 * @return string
	 */
	public static function tableName(){
		return 'user_time_share';
	}

    public static function pk(){
		return self::$pk;
	}

	public static function getFields(){
		return self::$fields;
	}

	/**
	 * @return \Atom\Package\Routine\UserTimeShare
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
     * 查询
     * @param $data
     * @param array $params
     * @return array
     */
    public function getListByParams(array $params = array()){
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
			$res = $this->builder()->whereIn(self::$pk,$id)->where('status','=','1')->get();
		}else{
			$res = $this->builder()->where(self::$pk,'=',$id)->where('status','=','1')->get();
		}

		return $res;
    }

}
