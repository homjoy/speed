<?php
namespace Atom\Package\Approval;

use Atom\Package\Common\BaseQuery;

/**
 * 资产具体信息
 * @author haibinzhou@meilishuo.com
 * @since 2016-03-02
 */
class AssetsSupply extends BaseQuery{

    private static $pk = 'id';


    private static $fields = array(
        'id'       => 0,
        'name'     => '',
        'pid'      => 0,
        'type'     => 0,
        'memo'     => '',
        'status'   => 1,
    );

    /**
     * 数据库表名
     * @return string
     */
    public static function tableName(){

        return 'assets_supply';
    }

    public static function pk(){
        return self::$pk;
    }

    public static function database()
    {
        return 'administration';
    }

    public static function getFields(){
        return self::$fields;
    }

    /**
     * @return \Atom\Package\Approval\AssetsSupply
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
    public function getDataList(array $params = array(),$offset = 0,$limit = 100){

        $qb = $this->builder();
        if (!empty($params['id'])) {
            if (is_array($params['id'])) {
                $qb->whereIn('id', $params['id']);
            }else{
                $qb->where('id', '=', $params['id']);
            }
        }

        if (!empty($params['pid'])) {
            if (is_array($params['pid'])) {
                $qb->whereIn('pid', $params['pid']);
            }else{
                $qb->where('pid', '=', $params['pid']);
            }
        }

        if (!empty($params['type'])) {
            if (is_array($params['type'])) {
                $qb->whereIn('type', $params['type']);
            }else{
                $qb->where('type', '=', $params['type']);
            }
        }

        if (!isset($params['status'])) {
            $qb->where('status', '=', 1);
        }else{
            if (is_array($params['status'])) {
                $qb->whereIn('status', $params['status']);
            }else{
                $qb->where('status', '=', $params['status']);
            }
        }


        //TODO 根据查询参数params build sql.
//        $qb->hash(static::$pk);
//        $queryObj = $qb->getQuery();
//        echo $queryObj->getRawSql();
//        exit;
        $ret = $qb->offset($offset)->limit($limit)->get();
        return $ret;

    }
    public function  updateById($params){

        $pk = static::pk();
        if(isset($params[$pk])&& $params[$pk] > 0){
            $id = intval($params[$pk]);
            unset($params[$pk]);
            return $this->builder()
                ->where($pk,$id)->update($params);
        }

        return FALSE;
    }

}
