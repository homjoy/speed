<?php
namespace Atom\Package\Routine;

use Atom\Package\Common\BaseQuery;

/**
 * Class Usertime
 * @package Atom\Package\Routine
 */
class UserTime extends BaseQuery{

	private static $pk = 'time_id';

	private static $fields = array(
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
        'version'       => 1,
        'remind_type'   => '',
        'remind_time'   => 0,
		'status'        => 1,
	);

	/**
	 * 数据库表名
	 * @return string
	 */
	public static function tableName(){
		return 'user_time';
	}

	public static function pk(){
		return self::$pk;
	}

	public static function getFields(){
		return self::$fields;
	}

	/**
	 * @return \Atom\Package\Routine\Usertime
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
        $ret = $qb->where('status','=','1')
            ->get();
        return $ret;
    }

	/**
	 * 根据user_id查询
     * @param array $params
     * @return array
	 */
	public function getListByUserId(array $params = array()){
		$qb = $this->builder();

		//查询条件
		if (isset($params['user_id'])) {
			if (is_array($params['user_id'])) {
				$qb->whereIn('user_time_relation.relation_user_id', $params['user_id']);
			}else{
				$qb->where('user_time_relation.relation_user_id', '=', $params['user_id']);
			}
		}
        $where = '( (start_time <= :start_time and :start_time < end_time )  '
            .'or ( start_time < :end_time and :end_time <= end_time ) '
            .'or ( :start_time  <= start_time and end_time <= :end_time )) ';

        $offset = ($params['page'] - 1)*$params['page_size'];
        //TODO 根据查询参数params build sql.
        $ret = $qb->join('user_time_relation','user_time_relation.time_id','=','user_time.time_id')
			->select('user_time.*')
            ->where($qb->raw($where,array(
                'start_time' => $params['start_time'],
                'end_time' => $params['end_time'],
            )))
			->where('user_time.status','=',1)
			->where('user_time_relation.status','=',1)
            ->orderBy('user_time.start_time','DESC')
            ->limit($params['page_size'])
            ->offset($offset)
			->get();

        return $ret;
	}

    /**
     * 根据user_id查询条数
     * @param array $params
     * @return array
     */
    public function getListCountByUserId(array $params = array()){
        $qb = $this->builder();

        //查询条件
        if (isset($params['user_id'])) {
            if (is_array($params['user_id'])) {
                $qb->whereIn('user_time_relation.relation_user_id', $params['user_id']);
            }else{
                $qb->where('user_time_relation.relation_user_id', '=', $params['user_id']);
            }
        }
        $where = '( (start_time <= :start_time and :start_time < end_time )  '
            .'or ( start_time < :end_time and :end_time <= end_time ) '
            .'or ( :start_time  <= start_time and end_time <= :end_time )) ';

        //TODO 根据查询参数params build sql.
        $ret = $qb->join('user_time_relation','user_time_relation.time_id','=','user_time.time_id')
            ->select('user_time.user_id')
            ->where($qb->raw($where,array(
                'start_time' => $params['start_time'],
                'end_time' => $params['end_time'],
            )))
            ->where('user_time.status','=',1)
            ->where('user_time_relation.status','=',1)
            ->orderBy('user_time.start_time','DESC')
            ->count();

        return $ret;
    }

    /**
     * 根据user_id检测个人时间冲突
     * @param array $params
     * @return array
     */
    public function checkUserTimeByUser(array $params = array()){
        $qb = $this->builder();

        //查询条件
        if (isset($params['user_id'])) {
            if (is_array($params['user_id'])) {
                $qb->whereIn('user_time_relation.relation_user_id', $params['user_id']);
            }else{
                $qb->where('user_time_relation.relation_user_id', '=', $params['user_id']);
            }
        }
        if(isset($params['time_id'])){
            $qb->where('user_time.time_id', '!=', $params['time_id']);
        }
        $where = '( (start_time <= :start_time and :start_time < end_time ) '
            .'or ( start_time < :end_time and :end_time <= end_time ) '
            .'or ( :start_time  <= start_time and end_time <= :end_time )) ';

        //TODO 根据查询参数params build sql.
        $ret = $qb->join('user_time_relation','user_time_relation.time_id','=','user_time.time_id')
            ->select('user_time_relation.*')
            ->where($qb->raw($where,array(
                'start_time' => $params['start_time'],
                'end_time' => $params['end_time'],
            )))
            ->where('user_time.status','=',1)
            ->where('user_time_relation.status','=',1)
            ->get();
        $res = array();
        if($ret){
            foreach($ret as $r){
               $res[] = $r['relation_user_id'];
            }
            //去重
            $res = array_unique($res);
        }
        return $res;
    }

    /**
     * 插入或者更新
     * @param $data
     * @return array|string
     */
    public function insertOrUpdate($data){
        $pk = static::pk();
        if(isset($data[$pk])  && $data[$pk] > 0){
            $id = intval($data[$pk]);
            unset($data[$pk]);
            //版本号自增
            $data['version'] = $this->builder()->raw('version+1');
            return $this->builder()
                ->where($pk,$id)->update($data);
        }else{
            //初始版本号
            $data['version'] = 1;
            return $this->builder()
                ->onDuplicateKeyUpdate($data)->insert($data);
        }
    }

    /**
     * 逻辑删除数据
     * @param $id
     * @return mixed
     */
    public function deleteLogicalById($id){
        if(empty($id)){
            throw new \InvalidArgumentException(__METHOD__." wrong parameter id.");
        }
        $builder = $this->builder();

        $param = array('status' => 0);
        //版本号自增
        $param['version'] = $builder->raw('version+1');
        if(is_array($id)){
            return $builder->whereIn(static::pk(),$id)->update($param);
        }else{
            return $builder->where(static::pk(), $id)->update($param);
        }
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
