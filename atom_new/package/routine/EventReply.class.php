<?php
namespace Atom\Package\Routine;

use Atom\Package\Common\BaseQuery;

/**
 *
 * Class EventReply
 * @package Atom\Package\Routine
 */
class EventReply extends BaseQuery{

	const REPLY_ACCEPT = 1; //接受
	const REPLY_TENTATIVE = 2; //可能
	const REPLY_DECLINE = 3; //婉拒


	/**
	 * 字段列表
	 * @var array
	 */
	public static $fields = array(
		'reply_id'       => 0,
		'book_id'       => 0,
		'user_id'       => 0,
		'reply'       => EventReply::REPLY_ACCEPT,
		'reason'          => '',
		'status'        => 1,
	);

	/**
	 * 数据库表名
	 * @return string
	 */
	public static function tableName(){
		return 'event_reply';
	}

	public static function pk(){
		return 'reply_id';
	}

	/**
	 * 查询拒绝预定的用户
	 * @param $bookId
	 * @return array
	 */
	public function getDeclineUsers($bookId)
	{
		if(empty($bookId)){
			return array();
		}


		$builder = $this->builder();
		$result = $builder->where('book_id',$bookId)->select('user_id')->get();
		$userIds = array();
		if(empty($result)){
			return $userIds;
		}

		foreach($result as $r){
			$userIds[] = $r['user_id'];
		}

		return $userIds;
	}

    /**
     * 查询所有的拒绝预定的用户
     * @author haibinzhou
     * @date 2015-07-22
     * @return array
     */
    public function getRefuseUsers()
    {

        $builder = $this->builder();
        $result = $builder->select(array('book_id','user_id'))->get();
        $userIds = array();
        if(empty($result)){
            return $userIds;
        }

        return $result;
    }



	/**
	 * 消息回复
	 * @param $params
	 * @return int
	 */
	public function reply($params)
	{
		if(empty($params) || empty($params['book_id']) || empty($params['user_id'])){
			return 0;
		}

		$params = array_intersect_key($params, static::$fields);
		$params = array_merge(static::$fields, $params);
		return $this->builder()->insert($params);
	}

	/**
	 * 更新之前的回复
	 * @param $params
	 * @return $this
	 */
	public function updateReply($params)
	{
		if(empty($params) || empty($params['reply_id']) || empty($params['reply'])){
			return 0;
		}

		$reply['reply'] = isset($params['reply']) ? $params['reply'] : EventReply::REPLY_ACCEPT;
		$reply['reason'] = isset($params['reason']) ? $params['reason'] : '';
		$reply['status'] = 1;
		$reply['create_time'] = date('Y-m-d H:i:s'); //回复时间

		$ret = $this->builder()->where('reply_id',$reply['reply_id'])->update($reply);
		return $ret;
	}

	/**
	 * 查询回复
	 * @param $bookId
	 * @param $userId
	 * @return array|null|\stdClass
	 */
	public function getReply($bookId,$userId)
	{
		if(empty($bookId) || empty($userId)){
			return array();
		}

		$builder = $this->builder()->where('status',1);
		$result = $builder->where('book_id',$bookId)->where('user_id',$userId)->first();
		return $result;
	}
}
