<?php

namespace Admin\Modules\Hr\Attendance;

use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Attendance\WorkingHours;
use Admin\Package\Log\Log;
/**
 * 考勤组管理
 * Created by guojiezhu@meilishuo.com
 * User: MLS  PersonalLeave
 * Date: 15/11/13
 * Time: 下午12:18
 */
class AjaxAttendanceUpdateAdd extends BaseModule {

    protected $errors = null;
    private $params = null;
    //protected $checkUserPermission = true;
    public static $VIEW_SWITCH_JSON = TRUE;
    protected static $LEAVE_TYPE = 6;
    
    public function run() {
        $this->_init();
        if ($this->params['id'] <= 0) {
            $insert_id = WorkingHours::getInstance()->createWorkingHours($this->params);
            if ($insert_id > 0) {
                $log_info = array('user_id'=>$this->user['id'],
                                  'handle_id'=> $insert_id,
                                  'operation_type'=>  'add',
                                  'after_data'=>  json_encode($this->params),
                                  'handle_type'=>self::$LEAVE_TYPE);

                $ret = Log::getInstance()->createLogs($log_info);
                $return = Response::gen_success('操作成功');
            } else {
                $return = Response::gen_error(Response::DS_DATA_NO_DATA, '', '插入数据库失败');
            }
        } else {
            $update_id = WorkingHours::getInstance()->updateWorkingHours($this->params);
                //记录日志
            $log_info = array('user_id'=>$this->user['id'],'handle_id'=> $this->params['id'],
            'operation_type'=>  'update','after_data'=>  json_encode($this->params),'handle_type'=>self::$LEAVE_TYPE);
      
            $ret = Log::getInstance()->createLogs($log_info);
            if ($update_id > 0) {
                $return = Response::gen_success('操作成功');
            } else {
                $return = Response::gen_error(Response::DS_DATA_NO_DATA, '', '更新操作失败');
            }
        }
        return $this->app->response->setBody($return);
    }

    private function _init() {
        $this->rules = array(
            'name' => array(// 邮箱 姓名 拼音
                'type' => 'string',
                'required' => true,
                'allowEmpty' => false,
            ),
            'morning_start' => array(
                'type' => 'string',
                'required' => true,
                'allowEmpty' => false,
            ),
            'morning_end' => array(
                'type' => 'string',
                'required' => true,
                'allowEmpty' => false,
            ),
            'afternoon_start' => array(
                'type' => 'string',
                'required' => true,
                'allowEmpty' => false,
            ),
            'afternoon_end' => array(
                'type' => 'string',
                'required' => true,
                'allowEmpty' => false,
            ),
            'id' => array(
                'type' => 'string',
                'default' => 0
            ),
        );

        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }

}
