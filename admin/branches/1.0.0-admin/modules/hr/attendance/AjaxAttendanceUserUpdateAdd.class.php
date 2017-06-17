<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 15-11-17
 * Time: 上午11:57
 */

namespace Admin\Modules\Hr\Attendance;

use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Attendance\WorkingHours;
use Admin\Package\Account\UserInfo;
use Admin\Package\Log\Log;
/**
 * Created by guojiezhu@meilishuo.com
 * User: MLS  PersonalLeave
 * Date: 15/11/13
 * Time: 下午12:18
 */
class AjaxAttendanceUserUpdateAdd extends BaseModule {

    protected $errors = null;
    private $params = null;
    //protected $checkUserPermission = true;
    public static $VIEW_SWITCH_JSON = TRUE;
    protected static $LEAVE_TYPE = 6;
    public function run() {
        $this->_init();
        if ($this->params['id'] <= 0) {
            $query_user_params = array(
                'user_id' => $this->params['user_id'],
                'status' => array(1,3)
            );
            $userInfo = UserInfo::getInstance()->getUserInfo($query_user_params);
            $userInfo  = current($userInfo);
            if(empty($userInfo)){
                $return = Response::gen_error(Response::DS_DATA_NO_DATA, '', '用户不存在在或者已离职');
                return $this->app->response->setBody($return);
            }
            $create_data = array(
                'user_id' => $this->params['user_id'],
                'work_id' => $this->params['work_id'],
                'staff_id' => $userInfo['staff_id'],
                'name_cn' => $userInfo['name_cn'],
                'status' => $userInfo['status'],
            );

            $insert_id = WorkingHours::getInstance()->createStaffRelation($create_data);
            if ($insert_id > 0) {
                $log_info = array('user_id'=>$this->user['id'],
                                  'handle_id'=> $insert_id,
                                  'operation_type'=>  'add',
                                  'after_data'=>  json_encode($create_data),
                                  'handle_type'=>self::$LEAVE_TYPE);
                $ret = Log::getInstance()->createLogs($log_info);
                $return = Response::gen_success('操作成功');
            } else {
                $return = Response::gen_error(Response::DS_DATA_NO_DATA, '', '插入数据库失败');
            }
        } else {
            $update_id = WorkingHours::getInstance()->updateStaffRelation($this->params);
            //记录日志
            if ($update_id > 0) {
                $log_info = array('user_id'=>$this->user['id'],'handle_id'=> $this->params['id'],
                                  'operation_type'=> 'update','after_data'=>  json_encode($this->params),'handle_type'=>self::$LEAVE_TYPE);

                $ret = Log::getInstance()->createLogs($log_info);
                $return = Response::gen_success('操作成功');
            } else {
                $return = Response::gen_error(Response::DS_DATA_NO_DATA, '', '更新操作失败');
            }
        }

        return $this->app->response->setBody($return);
    }

    private function _init() {
        $this->rules = array(
            'work_id' => array(
                'type' => 'string',
                'required' => true,
                'allowEmpty' => false,
            ),
            'user_id' => array(
                'type' => 'string',
                'required' => true,
                'allowEmpty' => false,
            ),
            'id' => array(
                'type' => 'string',
                'default' => 0
            ),
            'status'  => array(
                'type' => 'integer',
                'default' => 3
            ),
        );

        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }

}
