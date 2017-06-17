<?php
namespace Atom\Package\Routine;

use Atom\Package\Common\BaseQuery;

/**
 * Class UsertimeRelation
 * @package Atom\Package\Routine
 */
class UserTimeRelation extends BaseQuery{

	private static $pk = 'id';

	private static $fields = array(
		'id'			=> 0,
		'time_id'       => 0,
		'relation_user_id'=> 0,
		'status'        => 1,
	);

	/**
	 * 数据库表名
	 * @return string
	 */
	public static function tableName(){
		return 'user_time_relation';
	}

	public static function pk(){
		return self::$pk;
	}

	public static function getFields(){
		return self::$fields;
	}

	/**
	 * @return \Atom\Package\Routine\UsertimeRelation
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

	/**
	 * 根据time_id逻辑删除
     * @param $time_id
     * @return array
	 */
	public function deleteLogicalByTimeId($time_id){
		$param = array('status' => 0);
		if(is_array($time_id)){
			return $this->builder()->whereIn('time_id',$time_id)->update($param);
		}else{
			return $this->builder()->where('time_id', $time_id)->update($param);
		}
	}

    /**
     * 更新
     * @param $data
     * @return array|string
     */
    public function updateDate($data){
        $res = $this->builder()
            ->where('time_id',$data['time_id'])
            ->where('relation_user_id',$data['relation_user_id'])
            ->update($data);
        return $res;
    }
}
