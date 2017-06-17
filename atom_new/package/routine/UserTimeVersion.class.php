<?php
namespace Atom\Package\Routine;

use Atom\Package\Common\BaseQuery;

/**
 * Class UsertimeVersion
 * @package Atom\Package\Usertime
 */
class UserTimeVersion extends BaseQuery{

	private static $pk = 'id';

	private static $fields = array(
		'id'			 => 0,
		'time_id'       => 0,
		'user_id'       => 0,
		'start_time'    => '0000-00-00 00:00:00',
		'end_time'      => '0000-00-00 00:00:00',
		'update_time'   => '0000-00-00 00:00:00',
		'color'         => '',
		'title'         => '',
		'position'      => '',
		'memo'          => '',
		'need_remind'   => 0,
		'user_id_json'  => '',
		'version'		=> 1,
        'remind_type'   => '',
        'remind_time'   => 0,
		'operate'		=> '',
		'is_operate'	=> 0,
	);

	/**
	 * 数据库表名
	 * @return string
	 */
	public static function tableName(){
		return 'user_time_version';
	}

	public static function pk(){
		return self::$pk;
	}

	public static function getFields(){
		return self::$fields;
	}

	/**
	 * @return \Atom\Package\Routine\UsertimeVersion
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
				if($k == 'start_time'){
					$qb->where($k,'>=',$v);
				}elseif($k == 'end_time'){
					$qb->where($k,'<=',$v);
				}elseif($k == 'title'){
					$qb->where($k,'LIKE','%'.$v.'%');
				}else{
					if(is_array($v)){
						$qb->whereIn($k, $v);
					}else{
						$qb->where($k,$v);
					}
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

    /**
     * 修改发送记录
     * @param $time_id
     * @param $version
     * @return mixed
     */
    public function updateOperateById($time_id,$version){
        if(empty($time_id)){
            throw new \InvalidArgumentException(__METHOD__." wrong parameter id.");
        }

        $param = array('is_operate' => 1);

        if(is_array($time_id)){
            return $this->builder()->whereIn('time_id',$time_id)->where('version','<=',$version)->update($param);
        }else{
            return $this->builder()->where('time_id', $time_id)->where('version','<=',$version)->update($param);
        }
    }

}
