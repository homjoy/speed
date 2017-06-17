<?php
namespace Atom\Package\Routine;

use Atom\Package\Common\BaseQuery;

/**
 * Class BookSign
 * @package Atom\Package\Routine
 */
class BookSign extends BaseQuery{

	private static $pk = 'id';

	private static $fields = array(
		'id'            => 0,
		'book_id'       => 0,
		'user_id'       => 0,
		'book_start'    => '0000-00-00 00:00:00',
		'sign_time'     => '0000-00-00 00:00:00',
		'sign_status'   => 0,
        'status'        => 1,
		'create_time'   => '0000-00-00 00:00:00',
	);

	/**
	 * 数据库表名
	 * @return string
	 */
	public static function tableName(){
		return 'book_sign';
	}

	public static function pk(){
		return self::$pk;
	}

	public static function getFields(){
		return self::$fields;
	}

	/**
	 * @return \Atom\Package\Routine\BookSign
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
        //签到时间控制在会议开始时间前后10分钟
        if(isset($params['book_start']) && isset($params['book_end'])){
            $where = ' book_start >= :book_start and book_start <= :book_end ';
        }

        //查询条件
        if(isset($params['user_id'])){
            $qb->where('user_id',$params['user_id']);
        }

        if(isset($params['book_id'])){
            $qb->where('book_id',$params['book_id']);
        }



        //TODO 根据查询参数params build sql.
        $ret = $qb->where('status','=','1')
            ->where('sign_status','=','0')
            ->where($qb->raw($where,array(
                'book_start' => $params['book_start'],
                'book_end' => $params['book_end'],
            )))
            ->get();
        return $ret;
    }

    /**
     * 更新Start_time
     * @param int $book_id
     * @param array $data
     * @return array
     */
    public function updateTimeByBookId($book_id,$data){
        if(empty($book_id) || empty($data['book_start'])){
            throw new \InvalidArgumentException(__METHOD__." wrong parameter params.");
        }
        $qb = $this->builder();
        $res = $qb->where('book_id',$book_id)
            ->update($data);

        return $res;
    }

    /**
     * 删除
     * @param int $book_id
     * @return array
     */
    public function deleteByBookId($book_id){
        if(empty($book_id)){
            throw new \InvalidArgumentException(__METHOD__." wrong parameter book_id.");
        }
        $param = array('status' => 0);
        $qb = $this->builder();

        return $qb->where('book_id',$book_id)->update($param);
    }

    /**
     * 签到接口
     * @param int $params
     * @return array
     */
    public function signConfirm($params){
        if(empty($params['book_id']) || empty($params['user_id'])){
            throw new \InvalidArgumentException(__METHOD__." wrong parameter params.");
        }
        $date = date('Y-m-d H:i:s');
        $param = array(
            'sign_status'   => 1,
            'sign_time'     => $date,
        );
        $qb = $this->builder();

        return $qb->where('book_id',$params['book_id'])->where('user_id',$params['user_id'])->update($param);
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
     * 根据book_id查询
     * @param int $id
     * @return array
     * @author haibinzhou
     * @date 2015-07-24
     */
    public function getDataByBookId($book_id){

        if(is_array($book_id)){
            $res = $this->builder()->whereIn('book_id',$book_id)->where('status','=','1')->get();
        }else{
            $res = $this->builder()->where('book_id','=',$book_id)->where('status','=','1')->get();
        }

        return $res;
    }

}
