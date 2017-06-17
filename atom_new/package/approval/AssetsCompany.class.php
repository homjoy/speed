<?php
namespace Atom\Package\Approval;

use Atom\Package\Common\BaseQuery;

/**
 * 固定资产供货公司信息
 * @author haibinzhou@meilishuo.com
 * @since 2016-03-02
 */
class AssetsCompany extends BaseQuery{

    private static $pk = 'id';

    private static $fields = array(
        'id'       => 0,
        'name'     => '',
        'memo'     => '',
        'status'   => 1,
    );

    /**
     * 数据库表名
     * @return string
     */
    public static function tableName(){
        return 'assets_company';
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
     * @return \Atom\Package\Approval\AssetsCompany
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

        if (!isset($params['status'])) {
            $qb->where('status', '=', 1);
        }else{
            if (is_array($params['status'])) {
                $qb->whereIn('status', $params['status']);
            }else{
                $qb->where('status', '=', $params['status']);
            }
        }
       if(isset($params['count'])&&$params['count']==1){
           return $qb->count();
       }

        //TODO 根据查询参数params build sql.
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
