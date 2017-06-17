<?php
namespace Admin\Modules\Meeting;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Company\City;
use Admin\Package\Meeting\Meeting;
use Admin\Package\Meeting\RoomService;
use Admin\Package\Account\UserInfo;
use  Libs\Util\ArrayUtilities;

/**
 * AjaxRoomInfo
 * Class
 *
 * @package Admin\Modules\Meeting
 */
class AjaxRoomInfo extends BaseModule {

    protected $errors = NULL;
    private   $params = NULL;
    public static $VIEW_SWITCH_JSON = TRUE;
    public function run() {

        $this->_init();
        $return =array();
        if( $this->post()->hasError()){
            $return = Response::gen_error(10001,$this->errors);
            return $this->app->response->setBody($return);
        }

        //room_service_rule
        $room_service_rule =RoomService::getInstance()->roomServiceRuleGet($this->params);

        $room_service =RoomService::getInstance()->roomServiceAll(array());
        $room_service =$this->valueToKey($room_service,'service_id');
        $temp=array();

        foreach($room_service_rule as $k=>$v){
            //处理配置
            $users =array();
            $t_d = json_decode($v['config'],true);
            if(!empty($t_d['user'])){
                $users = $t_d;
            }
            if(!isset($t_d['user'])){
                $users = json_decode($room_service[$v['service_id']]['config'],true);
            }

            if(isset($users['user'])){
                $users =  UserInfo::getInstance()->getUserInfo(array('user_id'=>$users['user']));
                $users =  ArrayUtilities::my_array_column($users,'name_cn');
                $users =  implode(",", $users);
                $temp['config'] =$users;
                $temp['name'] =$room_service[$v['service_id']]['name'];
                $return[] =$temp;
            }

        }

        $this->app->response->setBody(Response::gen_success($return));
    }

    /**
     * 返回数组中指定的一列值为索引的数组
     */
    public  function  valueToKey($array=array(),$col){

        $results=array();
        if(!is_array($array)) return $results;

        foreach($array as &$value){
            if(isset($value[$col])){
                $results[$value[$col]] =$value;
            }
        }
        return $results;
    }
    private function _init() {

        $this->rules = array(
            'room_id'  => array(
                'required'	=> TRUE,
                'allowEmpty' => FALSE,
                'type'    => 'integer',
            )

        );
        $this->params = $this->post()->safe();
        $this->errors = $this->post()->getErrors();
    }


}