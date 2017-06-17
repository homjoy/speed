<?php
namespace Admin\Modules\Hr\Working_calendar;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Hr_leave\WorkingCalendarList;
use Admin\Package\Log\Log;
/**
 * 添加或者修改工作日历
 * Class AjaxWorkingCalendarUpsert
 * @package Admin\Modules\Hr\Working_calendar
 */
class AjaxWorkingCalendarUpsert extends BaseModule
{

    protected $params = array();
    protected $errors = array();
    public static $VIEW_SWITCH_JSON = TRUE;

    public function run()
    {
        $this->_init();
        if ($this->query()->hasError()) {
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }

        if(!empty($this->params['id']) ){
            //修改
            $info = WorkingCalendarList::getInstance()->updateCalendarInfo($this->params);
            if(intval($info) >0){
                $return = Response::gen_success(array());
            }else{
                $return = Response::gen_error(50012,'','无数据更新');
            }
            $log_info = array(
                'user_id'        => $this->user['id'],
                'handle_id'      => $this->params['id'],
                'operation_type' => 'update',
                'after_data'     => json_encode($this->params),
                'handle_type'    => 11
            );
        }else{
            //增加
            $info = WorkingCalendarList::getInstance()->createCalendarInfo($this->params);
            if(intval($info) >0){
                $return = Response::gen_success(array());
            }else{
                $return = Response::gen_error(50012,'插入失败');
            }
            $log_info = array(
                'user_id'        => $this->user['id'],
                'handle_id'      => 0,
                'operation_type' => 'add',
                'after_data'     => json_encode($this->params),
                'handle_type'    => 11
            );

        }

        Log::getInstance()->createLogs($log_info);

        $this->app->response->setBody($return);
    }

    /**
     * 参数初始化
     */
    protected function _init()
    {
        $this->rules = array(
            'id' => array(
                'type' => 'integer',

            ),
            'date' => array(
                'type' => 'string',
                'default' => '',
            ),
            'type' => array(
                'type' => 'integer',
                'default' => 1,
            ),
            'title' => array(
                'type' => 'string',
                'default' => '',

            ),
            'status' => array(
                'type' => 'integer',
                'default' => 1,
            ),
        );

        $this->params = $this->post()->safe();
    }

}
