<?php
namespace Atom\Package\Routine;

use Atom\Package\Common\BaseQuery;

/**
 *
 * Class BookInfo
 * @package Atom\Package\Routine
 */
class BookInfo extends BaseQuery{


	const REPEAT_NO = 0;  //不重复
	const REPEAT_DAY = 1;	//按天重复
	const REPEAT_WEEK = 7;	//按周重复
	const REPEAT_MONTH = 30;	//按月重复

	const TYPE_NORMAL = 1; //普通会议
	const TYPE_VIDEO_CAMERA = 2; //视频会议
	const TYPE_PHONE = 3; //电话会议

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
		'status'        => 1,
		'version'        => 1,
	);

	/**
	 * 数据库表名
	 * @return string
	 */
	public static function tableName(){
		return 'book_info';
	}

	public static function pk(){
		return 'book_id';
	}

	/**
	 * 按ID 查询预定
	 * @param $id
	 * @param bool $only	是否只查询本身
	 * @return array
	 */
	public function getById($id,$only = false)
	{
		if(empty($id)){
			throw new \InvalidArgumentException(__METHOD__." wrong parameter id.");
		}

		$book = $this->builder()->where('status',1)->where(static::pk(),$id)->first();
		if($only){
			return $book;
		}
		if(empty($book) || !isset($book['book_id'])){
			return array();
		}

		//当前查询的ID是分会场
		//重新使用主预定ID查询
		$mainBookId = $book['main_book_id'] != 0 ? $book['main_book_id'] : $book['book_id'];

		return $this->builder()->where('status',1)->where(function($builder) use($mainBookId){
			$builder->where('book_id',$mainBookId)->orWhere('main_book_id',$mainBookId);
		})->get();
	}

	/**
	 * @param $bookInfo
	 * @return array|string
	 */
	public function addMeetingBook($bookInfo)
	{
		$params = array_intersect_key($bookInfo, static::$fields);
		$params = array_merge(static::$fields, $params);
		unset($params[static::pk()]);
		//初始版本号
		$params['version'] = 1;

		return $this->builder()->insert($params);
	}

	/**
	 * @param $bookInfo
	 * @return array|string
	 */
	public function updateMeetingBook($bookInfo)
	{
		if(empty($bookInfo[static::pk()])){
			return false;
		}
		$builder = $this->builder();
		$params = array_intersect_key($bookInfo, static::$fields);
		$pk = $bookInfo[static::pk()];
		unset($bookInfo['book_id']);
		//版本号自增
		$params['version'] = $builder->raw('version+1');

		return $builder->where('status',1)->where(static::pk(),$pk)
			->update($params);
	}

	/**
	 * 修改会议室预定时间
	 * @param $params
	 * @return $this|bool
	 */
	public function updateBooksTime($params)
	{
		if(!isset($params['book_id']) || !isset($params['book_start']) || !isset($params['book_end'])){
			return false;
		}
		$builder = $this->builder();

		$startTime = strtotime($params['book_start']);
		$endTime = strtotime($params['book_end']);
		$params['book_date'] = date('Y-m-d',$startTime);
		$params['time_start'] = date('H:i:s',$startTime);
		$params['time_end'] = date('H:i:s',$endTime);
		$params['month'] = date('m',$startTime);
		$params['month_day'] = date('d',$startTime);
		$params['week_day'] = date('N',$startTime);
		$params['version'] = $builder->raw('version+1');

		//版本号自增
		$bookIds = is_array($params['book_id']) ? $params['book_id'] : array($params['book_id']);
		unset($params['book_id']);
		return $builder->whereIn(static::pk(),$bookIds)->update($params);
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	public function deleteBooks($id)
	{
		if(empty($id)){
			return 0;
		}
		$builder = $this->builder();
		if(is_array($id)){
			$builder->whereIn(static::pk(),$id);
		}else{
			$builder->where(static::pk(), $id);
		}

		$param = array('status' => 0);
		//版本号自增
		$param['version'] = $builder->raw('version+1');

		return $builder->update($param);
	}


	/**
	 * @param $roomIds
	 * @param string $startTime
	 * @param string $endTime
	 * @param int $offset
	 * @param int $limit
	 * @return array|null
	 */
	public function getRoomBooks($roomIds, $startTime= '',$endTime = '',$offset = 0,$limit = 999)
	{
		$builder = $this->builder();

		$builder->where('status','=',1);
		if (!empty($roomIds)) {
			$roomIds = is_array($roomIds) ? $roomIds : array($roomIds);
			$builder->whereIn('room_id',$roomIds);
		}


		$builder->where(function($builder) use($startTime,$endTime){
			BookInfo::crossTime($builder,$startTime,$endTime);
		});

		$books = $builder->offset($offset)->limit($limit)->get();
		if(empty($books)){
			return array();
		}

		return static::fillRepeat($books,$startTime,$endTime);
	}


	/**
	 * @param \Pixie\QueryBuilder\QueryBuilderHandler $builder
	 * @param $startTime
	 * @param $endTime
	 * @return mixed
	 */
	public static function crossTime(&$builder,$startTime,$endTime)
	{
		$startTime = is_int($startTime) ? $startTime : strtotime($startTime);
		$endTime = is_int($endTime) ? $endTime : strtotime($endTime);
		$bookStart = date('Y-m-d H:i:s',$startTime);
		$bookEnd = date('Y-m-d H:i:s',$endTime);

		$builder->where('repeat_type','<>',static::REPEAT_NO);
//			if (!empty($startTime)) {
		//会议开始时间其他会议没有结束
		$builder->orWhere($builder->raw("(book_start <= '{$bookStart}' and '{$bookStart}' < book_end)"));
//			}

//			if (!empty($endTime)) {
		//会议结束时间其他会议已经开始了
		$builder->orWhere($builder->raw("(book_start < '{$bookEnd}' and '{$bookEnd}' <= book_end)"));
//			}

//			if (!empty($startTime) && !empty($endTime)) {
		//该时间段有其他会议~
		$builder->orWhere($builder->raw("('{$bookStart}' <= book_start and book_end <= '{$bookEnd}')"));
//			}
	}

	/**
	 * 填充重复的会议
	 * @param $data
	 * @param int $startTime
	 * @param int $endTime
	 * @return array
	 */
	public static function fillRepeat($data,$startTime = 0,$endTime = 0)
	{
		if (empty($data)) {
			return array();
		}

		$startTime = is_int($startTime) ? $startTime : strtotime($startTime);
		$endTime = is_int($endTime) ? $endTime : strtotime($endTime);

		//预定列表
		$bookList = array();
		foreach ($data as $index => $book) {
			switch($book['repeat_type']){
				//不重复的类型，直接放入
				case self::REPEAT_NO:
					$bookList[] = $book;
					break;

				case self::REPEAT_DAY:
				case self::REPEAT_WEEK:
				case self::REPEAT_MONTH:
					//合并内容
					$bookList = array_merge($bookList,static::repeatBook($book,$startTime,$endTime));
					break;
				default:
					break;
			}
		}

		return $bookList;
	}


	/**
	 * 生成重复会议
	 * @param $book
	 * @param $startTime
	 * @param $endTime
	 * @return array
	 */
	public static function repeatBook($book,$startTime,$endTime)
	{
		$bookList = array();
		$type = $book['repeat_type'];
		//会议开始时间
		$bookStart  = strtotime($book['book_start']);
		//会议结束时间
		$bookEnd    = strtotime($book['book_end']);
		//会议从几点开始
		$timeStart = date('H:i:s', $bookStart);
		//几点结束
		$timeEnd   = date('H:i:s', $bookEnd);
//		if($type == static::REPEAT_WEEK){
//			//一周
//			$timeInterval = 7*24*60*60;
//		}else{
//			//一天24小时
//			$timeInterval = 24*60*60;
//		}

		//一天24小时
		$timeInterval = 24*60*60;
		$initialStartTime = strtotime($book['book_date']);
		for($i = $startTime; $i <= $endTime; $i += $timeInterval){
			//周末不计算在内
			$weekDay = date('N',$i);
			//开始时间不可能超过最开始会议第一次预定的时间
			//if ($i < $initialStartTime || $weekDay > 5) {
			if ($i < $initialStartTime) {
				// || $weekDay > 5
				//不限制周末
				continue;
			}

			//按周重复的，不是每个周的同一天
			if(static::REPEAT_WEEK == $type && $weekDay != $book['week_day']){
				continue;
			}
			//如果是按月重复的，
			if(static::REPEAT_MONTH == $type && date('d',$i) != $book['month_day'] ){
				continue;
			}

			$date = date('Y-m-d ',$i);
			$book['book_start']   = $date. $timeStart;
			$book['book_end']     = $date. $timeEnd;

			$start = strtotime($book['book_start']);
			$end = strtotime($book['book_end']);

			//针对头尾两天，该会议的时间不在范围内
			//生成出来的会议时间必须
			if($end <= $startTime || $endTime <= $start){
				continue;
			}

			$bookList[] = $book;
		}

		return $bookList;
	}


	/**
	 * 获取会议室在指定日期、时间范围内的所有预定
	 * @param $type
	 * @param $roomIds
	 * @param $startTime
	 * @param $endTime
	 * @return null|\stdClass
	 */
	public function getRepeatBooks($type,$roomIds,$startTime,$endTime)
	{
		if(empty($startTime) || empty($endTime)){
			throw new \InvalidArgumentException("查询参数不能为空");
		}

		$builder = $this->builder()->where('status','=',1);
		if (!empty($roomIds)) {
			$roomIds= is_array($roomIds) ? $roomIds : array($roomIds);
			$builder->whereIn('room_id',$roomIds);
		}


		$startTime = is_int($startTime) ? $startTime : strtotime($startTime);
		$endTime = is_int($endTime) ? $endTime : strtotime($endTime);
		$bookStart = date('Y-m-d H:i:s',$startTime);
		$bookEnd = date('Y-m-d H:i:s',$endTime);
		$date = date('Y-m-d',$startTime);
		$timeStart = date('H:i:s',$startTime);
		$timeEnd = date('H:i:s',$endTime);

		//跟给定的时间有任何交集部分都算在内
		/*
		 *
		 * ============time_start===========time_end=============
		 * 1===========
		 * 2										=============
		 * 3 					 ===========
		 * 检测以上三段时间
		 */
		$where = '( (time_start <= :time_start and :time_start < time_end )  '
			.'or ( time_start < :time_end and :time_end <= time_end ) '
			.'or ( :time_start  <= time_start and time_end <= :time_end )) ';
		$builder->where($builder->raw($where,array(
			'time_start'=>$timeStart,
			'time_end'=>$timeEnd,
		)));



		switch($type){
			//重复会议室，全部冲突，非重复会议室，大于start_time的冲突
			case static::REPEAT_DAY:
				$where = " ( ( repeat_type = ".static::REPEAT_NO." and book_start > :book_start) "
					."or repeat_type <> ".static::REPEAT_NO.") "; //任意重复的都会冲突
				break;
			case static::REPEAT_WEEK:
				//如果按周重复的话，不能出现临时会议出现在该天
				$where = " ( (repeat_type = ".static::REPEAT_NO." and week_day = :week_day and :book_start < book_start ) "
					." or repeat_type = ".static::REPEAT_DAY  //或者任意按天重复的，未来某一天也会冲突
					." or (repeat_type = ".static::REPEAT_WEEK." and week_day = :week_day) " //相同的按周重复
					." or repeat_type = ".static::REPEAT_MONTH.")"; //按周跟按月的也不能同时出现，迟早有一天也会冲突
				break;
			case static::REPEAT_MONTH:
				$where = " ( (repeat_type = ".static::REPEAT_NO." and month_day = :month_day and :book_start < book_start) "
					." or repeat_type = ".static::REPEAT_DAY  //或者任意按天重复的，未来某一天也会冲突
					." or repeat_type = ".static::REPEAT_WEEK  //任意按周重复的未来某一天也会冲突
					." or (repeat_type = ".static::REPEAT_MONTH." and month_day = :month_day) ) ";
				break;
			default:
				break;
		}

		$builder->where($builder->raw($where,array(
			'book_start' => $bookStart,
			'week_day'=>date('N',$startTime),
			'month_day'=>date('d',$startTime),
		)));

		return $builder->offset(0)->limit(999)->get();
	}

    /**
     * 获取会议室在指定日期重复预定
     * @param $date
     * @param $meetingRoomId
     * @return null|\stdClass
     */
    public function getRepeatByDay($date,$meetingRoomId = array()){
        if(empty($date)){
            throw new \InvalidArgumentException("查询参数不能为空");
        }
        $builder = $this->builder()->where('status','=',1);
        $where = '((repeat_type = 1 and book_date <= :book_date) '
            .'or (repeat_type = 7 and book_date <= :book_date and week_day = :week_day) '
            .'or (repeat_type = 30 and book_date <= :book_date and month_day = :month_day)) ';
        $builder->where($builder->raw($where,array(
            'book_date'=>$date,
            'week_day'=>intval(date('N')),
            'month_day'=>intval(date('j')),
        )));
        if(!empty($meetingRoomId)){
            $builder->whereIn('room_id', $meetingRoomId);
        }
        return $builder->offset(0)->limit(999)->hash('book_id')->get();
    }

	/**
	 * 修改分会场的关联.
	 * @param $bookId
	 * @param $mainBookId
	 * @return $this
	 */
	public function updateMainBookId($bookId,$mainBookId)
	{
		$builder = $this->builder()->where('book_id','=',$bookId);
		return $builder->update(array('main_book_id'=>$mainBookId));
	}

	/**
	 * 重复预定的列表（数据迁移记录初始version 用）
	 * @param $offset
	 * @param $limit
	 * @return null|\stdClass
	 */
	public function repeatBooks($offset,$limit)
	{
		$builder = $this->builder()->where('status','=',1);
		//所有重复的预定
		$builder->where('repeat_type','<>',0);
		return $builder->offset($offset)->limit($limit)->get();
	}

	/**
	 * 获取重复预定的数量.（数据迁移记录初始version 用）
	 * @return int
	 */
	public function repeatBooksCount()
	{
		$builder = $this->builder()->where('status','=',1);
		$builder->where('repeat_type','<>',0);
		return $builder->count();
	}
}
