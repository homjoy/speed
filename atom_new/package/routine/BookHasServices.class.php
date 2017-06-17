<?php
namespace Atom\Package\Routine;

use Atom\Package\Common\BaseQuery;

/**
 *
 * Class BookHasServices
 * @package Atom\Package\Routine
 */
class BookHasServices extends BaseQuery{

	/**
	 * 字段列表
	 * @var array
	 */
	public static $fields = array(
		'id'       => 0,
		'book_id'       => 0,
		'service_id'       => 0,
		'memo'       => '',
		'status'        => 1,
	);

	/**
	 * 数据库表名
	 * @return string
	 */
	public static function tableName(){
		return 'book_has_services';
	}

	public static function pk(){
		return 'id';
	}


	/**
	 * 获取会议的预定服务
	 * @param $bookId
	 * @return array
	 */
	public function getBookServices($bookId)
	{
		if(empty($bookId)){
			return array();
		}

		$builder = $this->builder();
		$builder->where('status','=',1);

		$bookId = is_array($bookId) ? $bookId : array($bookId);
		$builder->whereIn('book_id',$bookId);

		$result = $builder->get();

		if(empty($result)){
			return array();
		}

		return $result;
	}

    /**
     * 批量获取会议的预定服务
     * @param $bookId
     * @return array
     */
    public function bgetBookServices($params = array())
    {
        if(empty($params)){
            return array();
        }

        $builder = $this->builder();
        $builder->where('status','=',1);

        $builder->whereIn('book_id',$params['book_id']);

        $result = $builder->get();


        if(empty($result)){
            return array();
        }


        return $result;
    }

	/**
	 * 删除预定的服务
	 * @param $bookId
	 * @param null $serviceId
	 * @return $this|bool
	 */
	public function deleteBookService($bookId,$serviceId = null)
	{
		if(empty($bookId)){
			return false;
		}

		$param = array('status' => 0);
		$builder = $this->builder()->where('book_id', $bookId);


		if(!empty($serviceId)){
			$serviceId = is_array($serviceId)?$serviceId : array($serviceId);
			$builder->whereIn('service_id',$serviceId);
		}

		return $builder->update($param);
	}
}
