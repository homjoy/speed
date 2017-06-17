<?php
namespace Atom\Package\Routine;

use Atom\Package\Common\BaseQuery;

/**
 *
 * Class BookInfoVersion
 * @package Atom\Package\Routine
 */
class BookInfoVersion extends BaseQuery{

	/**
	 * 字段列表
	 * @var array
	 */
	public static $fields = array(
		'book_id'       => 0,
		'main_book_id'       => 0,
		'user_id'       => 0,
		'room_id'       => 0,
		'meeting_type'       => 1,
		'meeting_topic'       => '',
		'book_start'    => '0000-00-00 00:00:00',
		'book_end'    => '0000-00-00 00:00:00',
		'book_date'    => '0000-00-00',
		'time_start'      => '00:00:00',
		'time_end'      => '00:00:00',
		'month'         => 0,
		'month_day'         => 0,
		'week_day'      => 0,
		'timezone'          => 'Asia/Shanghai',
		'memo'          => '',
		'repeat_type'   => BookInfo::REPEAT_NO,
		'user_id_json'   => '',
		'service_id_json'   => '',
		'version'        => 0,
		'operate'        => '',
		'is_operate'        => 0,
	);

	/**
	 * 数据库表名
	 * @return string
	 */
	public static function tableName(){
		return 'book_info_version';
	}

	public static function pk(){
		return 'id';
	}

	/**
	 *
	 * @param string $operate
	 * @param array $versionBook
	 * @return array|string
	 */
	public function changed($operate = 'update',array $versionBook)
	{
		$params = array_intersect_key($versionBook, static::$fields);
		$params = array_merge(static::$fields, $params);

		if(!isset($params['version']) || empty($params['version'])){
			$params['version'] = 1;
		}else{
			$params['version'] = $params['version']+1;
		}

		$params['operate'] = $operate;
		$params['is_operate'] = 0;

		return $this->builder()->insert($params);
	}

    /**
     * 查询
     * @param array $params
     * @return array
     */
    public function getList(array $params = array()){
        $qb = $this->builder();
        //查询条件
        if (isset($params['is_operate'])) {
            $qb->where('is_operate', '=', $params['is_operate']);
        }
        if (isset($params['repeat_type'])) {
            $qb->where('repeat_type', '=', $params['repeat_type']);
        }
        if (isset($params['book_id'])) {
            $qb->where('book_id', '=', $params['book_id']);
        }
        if (isset($params['room_id'])) {
            if (is_array($params['room_id'])) {
                $qb->whereIn('room_id', $params['room_id']);
            }else{
                $qb->where('room_id', '=', $params['room_id']);
            }
        }
        //TODO 根据查询参数params build sql.
        $ret = $qb->orderBy('book_id')
            ->orderBy('version')
            ->get();

        return $ret;
    }

    /**
     * 查询
     * @param array $params
     * @return array
     */
    public function getRepeatList(array $params = array()){
        $qb = $this->builder();

        //查询条件
        if (isset($params['repeat_type'])) {
            $qb->where
            ('repeat_type', array(1,5,30));
        }
        if (isset($params['month'])) {
            $qb->orwhere('month', '=', $params['month']);
        }
        if (isset($params['month_day'])) {
            $qb->where('month_day', '=', $params['month_day']);
        }
        //TODO 根据查询参数params build sql.
        $ret = $qb->orderBy('book_id')
            ->orderBy('version')
            ->get();

        return $ret;
    }

    /**
     * 按ID 查询预定
     * @param $id
     * @return null|\stdClass
     */
    public function getBookSubById($id){
        if(empty($id)){
            throw new \InvalidArgumentException(__METHOD__." wrong parameter id.");
        }

        $builder = $this->builder()->where('status',1)->whereIn(static::pk(),$id);

        $builder->orWhereIn('main_book_id',$id);
        return $builder->hash(static::pk())->get();
    }

    /**
     * 修改发送记录
     * @param $book_id
     * @param $version
     * @return mixed
     */
    public function updateOperateById($book_id,$version){
        if(empty($book_id)){
            throw new \InvalidArgumentException(__METHOD__." wrong parameter id.");
        }

        $param = array('is_operate' => 1);

        if(is_array($book_id)){
            return $this->builder()->whereIn('book_id',$book_id)->where('version','<=',$version)->update($param);
        }else{
            return $this->builder()->where('book_id', $book_id)->where('version','<=',$version)->update($param);
        }
    }
}
