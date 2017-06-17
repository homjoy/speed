<?php
namespace Atom\Package\Routine;

use Atom\Package\Common\BaseQuery;
/**
 *
 * Class UserBooks
 * @package Atom\Package\Routine
 */
class UserBooks extends BaseQuery{
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
	 * 查询用户的预定列表
	 * @param $userIds
	 * @param string $startTime
	 * @param string $endTime
	 * @param int $offset
	 * @param int $limit
	 * @param int $room_id
	 * @return array
	 */
	public function getUserBooks($userIds, $startTime= '',$endTime = '',$offset = 0,$limit = 999,$room_id = array())
	{
		$builder = $this->builder();

		$builder->rightJoin('book_has_users','book_has_users.book_id','=','book_info.book_id');
		$builder->where('book_has_users.status',1);
		$builder->where('book_info.status',1);

        if(!empty($room_id)){
            $builder->whereIn('book_info.room_id', $room_id);
        }

		if (!empty($userIds)) {
			$userIds = is_array($userIds) ? $userIds : array($userIds);
			$builder->whereIn('book_has_users.user_id',$userIds);
		}

		$builder->select($builder->raw('book_info.*,book_info.user_id as founder_user_id,book_has_users.user_id as user_id'));
		//
		$builder->where(function($builder) use($startTime,$endTime){
			BookInfo::crossTime($builder,$startTime,$endTime);
		});

		$books = $builder->offset($offset)->limit($limit)->orderBy('book_info.book_start')->get();

		return BookInfo::fillRepeat($books,$startTime,$endTime);
	}
}
