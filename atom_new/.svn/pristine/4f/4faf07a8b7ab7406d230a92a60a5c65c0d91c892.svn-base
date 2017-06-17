<?php
namespace Atom\Package\Routine;

use Atom\Package\Common\BaseQuery;

/**
 *
 * Class BookHasUsers
 * @package Atom\Package\Routine
 */
class BookHasUsers extends BaseQuery{

	/**
	 * 字段列表
	 * @var array
	 */
	public static $fields = array(
		'id'       => 0,
		'book_id'       => 0,
		'room_id'       => 0,
		'user_id'       => 0,
		'status'        => 1,
	);

	/**
	 * 数据库表名
	 * @return string
	 */
	public static function tableName(){
		return 'book_has_users';
	}

	public static function pk(){
		return 'id';
	}


	/**
	 * 获取预定用户的ID 列表
	 * @param $bookId
	 * @param bool $onlyUid
	 * @return array
	 */
	public function getBookUserIds($bookId, $onlyUid = true)
	{

		if(empty($bookId)){
			return array();
		}

		$builder = $this->builder();
		$builder->where('status','=',1);

		$bookId = is_array($bookId) ? $bookId : array($bookId);
		$builder->whereIn('book_id',$bookId);

		$result = $builder->get();


		if(empty($result) || !$onlyUid){
			return array();
		}

		$userIds = array();
		if(!empty($result)){
			foreach($result as $bookUser){
				$userIds[$bookUser['book_id']][] = $onlyUid ? $bookUser['user_id'] : $bookUser;
			}
		}

		return $userIds;
	}


    /**
     *
     * 批量获取预定用户的ID 列表
     * @param $bookId
     * @return array
     *
     */

    public function bgetBookUserIds($params = array()){
        if(empty($params)){
            return array();
        }


        $params = array_pop($params);

        $builder = $this->builder();
        $builder->where('status','=',1);

        $builder->whereIn('book_id',$params);
        $result = $builder->get();

        if(empty($result)){
            return array();
        }

        $userIds = array();
        if(!empty($result)){
            foreach($result as $bookUser){

                $userIds[$bookUser['book_id']][] = $bookUser['user_id'];
            }
        }

        return $userIds;
    }

	/**
	 * 删除预定的参与用户
	 * @param $bookId
	 * @param null $userId
	 * @param array $exclude 不删除的人（预订者本人）
	 * @return $this|bool
	 */
	public function deleteBookUsers($bookId,$userId = null,$exclude = array())
	{
		if(empty($bookId) || empty($userId)){
			return false;
		}

		$param = array('status' => 0);
		$builder = $this->builder()->where('book_id', $bookId);
		if(!empty($exclude)){
			$builder->whereNotIn('user_id',is_array($exclude) ? $exclude : array($exclude));
		}

		if(!empty($userId)){
			$userId = is_array($userId)?$userId : array($userId);
			$userId = array_diff($userId,$exclude);
			$builder->whereIn('user_id',$userId);
		}

		return $builder->update($param);
	}

	/**
	 * 删除所有
	 * @param $bookIds
	 * @return $this|bool
	 */
	public function deleteBooks($bookIds)
	{
		if(empty($bookId)){
			return false;
		}

		$bookIds = is_array($bookIds)?$bookIds : array($bookIds);
		$param = array('status' => 0);
		$builder = $this->builder()->whereIn('book_id', $bookIds);
		return $builder->update($param);
	}
}
