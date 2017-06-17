<?php
namespace Atom\Package\Routine;

use Atom\Package\Common\BaseQuery;

/**
 * Class BookRepeatSend
 * @package Atom\Package\Routine
 */
class BookRepeatSend extends BaseQuery{

	private static $pk = 'id';

	private static $fields = array(
		'id'			=> 0,
		'book_id'       => 0,
		'send_time'     => '0000-00-00 00:00:00',
	);

	/**
	 * 数据库表名
	 * @return string
	 */
	public static function tableName(){
		return 'book_repeat_send';
	}

	public static function pk(){
		return self::$pk;
	}

	public static function getFields(){
		return self::$fields;
	}

	/**
	 * @return \Atom\Package\Routine\BookRepeatSend
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

        //查询条件
        if (isset($params['book_id'])) {
            $qb->where('book_id', '=', $params['book_id']);
        }
        if (isset($params['send_time'])) {
            $qb->where('send_time', 'like', "%{$params['send_time']}%");
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

}
