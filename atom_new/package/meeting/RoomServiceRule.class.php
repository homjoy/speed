<?php

namespace Atom\Package\Meeting;


use Atom\Package\Common\BaseQuery;

/**
 * Class RoomServiceRule
 * @package Atom\Package\Meeting
 */
class RoomServiceRule extends BaseQuery{

    const STATUS_OK = 'ok'; //正常服务
    const STATUS_CONDITION = 'condition'; //限制条件下可以
    const STATUS_PAUSE = 'pause';  //服务暂停
    const STATUS_STOP = 'stop';  //服务已停止
    const STATUS_ERROR = 'error';  //设备故障

    /**
     * 字段列表
     * @var array
     */
    public static $fields = array(
        'rule_id'       => 0,
        'room_id'       => 0,
        'service_id'       => 0,
        'service_status'       => self::STATUS_OK,
        'tips'       => '',
        'config'       =>  '',
        'status'        => 1,
    );


    public static function database(){
        return 'administration';
    }

    public static function tableName(){
        return 'room_service_rule';
    }

    public static function pk(){
        return 'rule_id';
    }

    /**
     * 给会议室增加服务规则
     * @param $rule
     * @return array|string
     */
    public function saveRule($rule)
    {
        if(empty($rule)){
            throw new \InvalidArgumentException('service rule不能为空');
        }

        //查询房间的服务，无视状态
        $roomService = $this->getRule($rule['room_id'],$rule['service_id'],null);
        $roomService = current($roomService);

        //之前没有，增加
        if(empty($roomService)){
            $roomService = $rule;
        }else{
            //有的，但是之前被删除了
            $roomService['status'] = 1;
        }

        //提供了配置的话
        if(isset($rule['config']) && !is_null($rule['config'])){
            $roomService['config'] = is_string($rule['config']) ? $rule['config'] : json_encode($rule['config']);
        }else{
            $roomService['config'] = '';
        }


        return $this->insertOrUpdate($roomService);
    }

    /**
     * 移除会议室提供的服务，
     * @param integer $roomId
     * @param null|integer|array $serviceId    为空时移除所有服务
     * @return $this
     */
    public function removeRuleFromRoom($roomId = null,$serviceId = null)
    {
        if(empty($roomId) && empty($serviceId)){
            throw new \InvalidArgumentException('room_id 或者 service_id 不能同时为空');
        }
        $builder = $this->builder();


        if(!empty($roomId)){
            if( is_array($roomId)){
                $builder->whereIn('room_id',$roomId);
            }else{
                $builder->where('room_id',$roomId);
            }
        }

        if(!empty($serviceId)){
            if( is_array($serviceId)){
                $builder->whereIn('service_id',$serviceId);
            }else{
                $builder->where('service_id',$serviceId);
            }
        }

        return $builder->update(array('status'=>0));
    }

    /**
     * 查询服务配置
     * @param $params
     * @return null|\stdClass
     */
    public function searchRule($params)
    {
        $builder = $this->builder();
        //会议室
        if(isset($params['room_id']) && !empty($params['room_id'])){
            $roomId = is_array($params['room_id']) ? $params['room_id'] : array($params['room_id']);
            $builder->whereIn('room_id',$roomId);
        }

        //状态
        if(isset($params['status']) && !is_null($params['status']) && $params['status'] !== ''){
            $builder->where('status','=',$params['status']);
        }

        //服务
        if(isset($params['service_id']) && !empty($params['service_id'])){
            $serviceId = is_array($params['service_id']) ? $params['service_id'] : array($params['service_id']);
            $builder->whereIn('service_id',$serviceId);
        }

        return $builder->get();
    }

    /**
     * 查询会议室服务
     * @param integer $roomId   会议室编号
     * @param null|integer|array $serviceId 为空时查询会议室所有服务
     * @param null|integer $status 状态
     * @return null|\stdClass
     */
    public function getRule($roomId,$serviceId = null, $status = null)
    {
        if(empty($roomId)){
            throw new \InvalidArgumentException('room_id 不能为空');
        }

        $params = array('room_id' => $roomId);

        if(!is_null($status) && $status !== ''){
            $params['status'] = $status;
        }

        if(!is_null($serviceId) && !empty($serviceId)){
            $params['service_id'] = $serviceId;
        }

        return $this->searchRule($params);
    }

    /**
     * @param $serviceId
     * @param null $status
     * @return null|\stdClass
     */
    public function getRuleByService($serviceId, $status = null)
    {
        if(empty($serviceId)){
            return array();
        }

        $params = array('service_id'=>$serviceId);
        if(!is_null($status) && $status !== ''){
            $params['status'] = $status;
        }

        return $this->searchRule($params);
    }

   /*
    * 获取所有的会议室时服务信息
    * @author haibinzhou
    * @date 2015-07-14
    */

    public function getAllService(){

        $builder = $this->builder();
        $result = $builder->where('status',1)->get();

        return $result;

    echo '<pre>';
     //   print_r($result);
    echo '</pre>';

    }
}