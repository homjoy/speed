<?php
namespace Atom\Package\Core;

use Atom\Package\Common\BaseQuery;

/**
 * Class Menu
 * @package Atom\Package\Core
 * @author haibinzhou@meilishuo.com
 * @date   2015-11-18
 */
class Menu extends BaseQuery{
    /**
     * @return string
     */
    public static function database()
    {
        return 'core';
    }

    /**
     * 数据库表名
     * @return string
     */
    public static function tableName(){
        return 'menu';
    }

    public static $col = array('id', 'parent_id', 'title', 'url', 'icon', 'highlight', 'newwindow', 'memo','status');

	private static $fields = array(
		'id'		=> 0,
		'parent_id'	=> 0,
		'title'		=> '',
		'url'		=> '',
		'icon'      => '',
        'highlight' => '',
        'newwindow' => '',
		'memo'      => '',
		'status'	=> 1,
	);


	public static function pk(){
		return 'id';
	}

	public static function getFields(){
		return self::$fields;
	}

	/**
	 * 查询
     * @param $data
	 * @param array $params
     * @return array
	 */
    public function getDataList($params = array()){
        if (!is_array($params)) {
            return FALSE;
        }
      //  print_r($params);die;

        //状态：默认有效
        if (!isset($params['status'])) {
            $params['status'] = array(1);
        }

        //匹配方式：默认严格匹配
        if (isset($params['match'])) {
            switch ($params['match']) {
                case 'like':
                    $params['match'] = 'LIKE';
                    break;
                case 'equal':
                default:
                    $params['match'] = '=';
                    break;
            }
        }else{
            $params['match'] = '=';
        }

        //查询
        $builder = $this->builder();
        $builder->select(static::$col);

        //id
        if (!empty($params['id'])) {
            if (is_array($params['id'])) {
                $builder->whereIn('id', $params['id']);
            }else{
                $builder->where('id', '=', $params['id']);
            }
        }

        //parent_id
        if (!empty($params['parent_id'])) {
            if (is_array($params['parent_id'])) {
                $builder->whereIn('parent_id', $params['parent_id']);
            }else{
                $builder->where('parent_id', '=', $params['parent_id']);
            }
        }

        //title
        if (!empty($params['title'])) {
            switch ($params['match']) {
                case 'LIKE':
                    $builder->where('title', 'LIKE', '%'.$params['title'].'%');
                    break;
                case '=':
                    $builder->where('title', '=', $params['title']);
                    break;
                default:
                    $builder->where('title', '=', $params['title']);
                    break;
            }
        }


        //状态
        if (is_array($params['status'])) {
            $builder->whereIn('status', $params['status']);
        }else{
            $builder->where('status', '=', $params['status']);
        }
        /*
                $builder->hash(static::pk());
                $queryObj = $builder->getQuery();
                echo $queryObj->getRawSql();
        */

        return $builder->hash(static::pk())->orderBy(static::pk(),'asc')->get();
    }

}
