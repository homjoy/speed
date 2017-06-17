<?php

namespace Atom\Package\Meeting;


use Atom\Package\Common\BaseQuery;

/**
 * Class RoomService
 * @package Atom\Package\Meeting
 */
class RoomService extends BaseQuery{

    const TYPE_HARDWARE = 0; //硬件设备
    const TYPE_SERVICE = 1;  //服务
    const TYPE_CATERING = 2; //餐饮

    /**
     * 字段列表
     * @var array
     */
    public static $fields = array(
        'service_id'       => 0,
        'name'       => '',
        'description'       => '',
        'multizone'       => 0,
        'type'       =>  self::TYPE_HARDWARE,
        'config'       =>  '',
        'status'        => 1,
    );

    public static function database(){
        return 'administration';
    }
    public static function tableName(){
        return 'room_service';
    }

    public static function pk(){
        return 'service_id';
    }


    /**
     *
     * 获取服务列表
     * @param $params
     * @param int $offset
     * @param int $limit
     * @return null|\stdClass
     */
    public function getServiceList($params,$offset = 0,$limit = 100)
    {
        $builder = $this->builder();

        /**
         * ID 列表
         */
        if(isset($params['service_id']) && !empty($params['service_id'])){
            $ids = is_array($params['service_id']) ? $params['service_id']  : array($params['service_id']);
            $builder->whereIn('service_id',$ids);
        }

        //是否支持多地会议
        if(isset($params['multizone'])){
            $builder->where('multizone','=',$params['multizone'] ? 1 : 0);
        }

        //服务类型
        if(isset($params['type'])){
            $builder->where('type','=',intval($params['type']));
        }

        //服务状态
        if(isset($params['status'])){
            $builder->where('status','=',$params['status'] ? 1 : 0);
        }

        return $builder->hash('service_id')->offset($offset)->limit($limit)->get();
    }

    /**
     * @param $service
     * @return array|string
     */
    public function saveService($service)
    {
        if(empty($service)){
            throw new \InvalidArgumentException('服务内容不能为空');
        }

        //提供了配置的话
        if(isset($service['config']) && is_array($service['config'])){
            $service['config'] = json_encode($service['config']);
        }

        return $this->insertOrUpdate($service);
    }

    /**
     * 删除服务
     * @param $serviceId
     * @return mixed
     */
    public function removeService($serviceId)
    {
        return parent::deleteLogicalById($serviceId);
    }

    /*
   * 获取所有的会议室服务
   * @author haibinzhou
   * @date 2015-07-14
   */

    public function serviceAll(){

        $builder = $this->builder();
        $result = $builder->where('status',1)->get();

        return $result;


    }
}