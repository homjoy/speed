<?php
namespace Atom\Package\Core;

use Atom\Package\Common\BaseQuery;

/**
 * Class Config
 * @package Atom\Package\Config
 */
class Config extends BaseQuery{

	private static $pk = 'id';

	private static $sample = array(
		'id'		=> 0,
		'path'		=> '',
		'key'		=> '',
		'value'		=> '',
		'memo'		=> '',
		'father_id'	=> 0,
		'status'	=> 1,
	);

	/**
	 * 数据库表名
	 * @return string
	 */
	public static function tableName(){
		return 'config';
	}

	public static function pk(){
		return self::$pk;
	}

	public static function getFields(){
		return self::$sample;
	}

	/**
	 * @return \Atom\Package\Core\Config
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
				$qb->where($k,$v);
			}
		}

        //TODO 根据查询参数params build sql.
        $ret = $qb->where('status','=','1')
			->hash(self::$pk)
            ->get();
        return $ret;
    }

	/**
	 * 查询
     * @param $data
	 * @param array $path
     * @return array
	 */
    public function getValue($path){
		$qb = $this->builder();

		if(!empty($path)){
			$qb->where('path',$path);
		}

        //TODO 根据查询参数params build sql.
        $ret = $qb->select('value')
			->where('status','=','1')
            ->first();
        return $ret;
    }

	/**
	 * 查询
     * @param $data
	 * @param array $path
     * @return array
	 */
    public function getChild($path){
		$qb = $this->builder();

		if(!empty($path)){
			$qb->where('path',$path);
		}

        //TODO 根据查询参数params build sql.
        $ret = $qb->select('id')
			->where('status','=','1')
            ->first();

		$ret = $this->getDataByFid($ret['id']);
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
	 * 根据father_id查询
     * @param int $id
     * @return array
	 */
    public function getDataByFid($f_id){
		$res = $this->builder()
            ->where('father_id','=',$f_id)
            ->where('status','=','1')
            ->orderBy('sort')
            ->get();
		return $res;
    }
    /**
     * 搜索
     * @param $data
     * @param array $params
     * @return array
     */
    public function getByPathOrFather($params=array(), $offset = 0, $limit = 100){
        $qb = $this->builder()
         ->where('status','=','1');
        if(isset($params['path'])){
            $qb->where('path', 'LIKE', '%'.$params['path'].'%');
        }
        if(isset($params['father_id'])){
            $qb->where('father_id','=',$params['father_id']);
        }
        if(isset($params['all']) && $params['all'] == 1){
            return $qb->hash(self::$pk)->get();
        }
        if(isset($params['count']) && $params['count'] == 1){
            return $qb->count();
        }
        return $qb->hash(self::$pk)->offset($offset)->limit($limit)->get();
    }

}
