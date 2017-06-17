<?php
namespace Admin\Modules\Meeting;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Meeting\RoomService;
use Admin\Package\Account\UserInfo;
/**
 * 会议室管理首页
 * Class Home
 *
 * @package Admin\Modules\Meeting
 */
class ServiceUpsert extends BaseModule {
	
	protected $errors = NULL;
	private   $params = NULL;
	//public static $VIEW_SWITCH_JSON = TRUE;
//    protected $checkUserPermission = TRUE;
	public function run() {
	    $this->_init();
        $room_service_all = RoomService::getInstance()->roomServiceAll();
        if(!empty($room_service_all)){
            foreach($room_service_all as $key=>&$val){
                $val['config'] = json_decode($val['config'],true);
            }
        }else{
            $room_service_all = array();
        }
        //获取当前会议室的人员信息
        if(!empty($this->params['id'])){
            $room_service_query = array(
                'room_id' => $this->params['id']
            );
            $room_service_rule_all = RoomService::getInstance()->roomServiceRuleGet($room_service_query);
            //整理数据
            empty($room_service_rule_all) && $room_service_rule_all = array();
            $new_room_service_all = array();
            foreach($room_service_rule_all as $room_value){
                $config = json_decode($room_value['config'],true);
                if($config){
                    if(!empty($config['user'])){
                        $default_user = array();
                        $user_info = UserInfo::getInstance()->getUserInfo(array('user_id' => $config['user']));
                        if(!empty($user_info)){
                            foreach($user_info as $user_val) {
                                $default_user[] = array(
                                    'user_id' => $user_val['user_id'],
                                    'name' => $user_val['name_cn'].'-'.$user_val['mail']
                                );
                            }
                        }
                        $room_value['config'] = $default_user;
                    }

                }

                $new_room_service_all[$room_value['service_id']] = $room_value;
            }

        }

        $return = Response::gen_success($room_service_all);
        $return['id'] = $this->params['id'];
        $return['service_rule'] = $new_room_service_all;

        return  $this->app->response->setBody($return);


	}


	private function _init() {
		
		$this->rules = array(
            'id'  => array(
                'type'    => 'integer',
                'default' => 0
            ),

		);
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
	}
	
}