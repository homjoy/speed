<?php
namespace Admin\Modules\Hr\Working_calendar;

use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Hr_leave\WorkingCalendarList;

/**
 * 日历 修改和添加
 * Class ConfigHome
 * @package Admin\Modules\Config
 * @author guojiezhu
 */
class WorkingCalendarUpsert extends BaseModule
{
    protected $errors = NULL;
    private $params = NULL;
    protected $checkUserPermisssion = TRUE;



    public function run()
    {

        $this->_init();

        if(!empty($this->params['id'])) {
            $query_params = array(
                'id' => $this->params
            );
            $result = WorkingCalendarList::getInstance()->getPageCalendarInfo($query_params);
            $result = current($result);
            $return = Response::gen_success($result);
        }else{
            $return = Response::gen_success(array());
        }
        $return['date_type'] = WorkingCalendarList::$date_type;
        $return['work_status'] = WorkingCalendarList::$work_status;
        $this->app->response->setBody($return);
    }

    private function _init()
    {

        $this->rules = array(
            'id' => array(
                'type' => 'integer',

            ),
            'date' => array(
                'required' => 'true',
                'allowEmpty' => FALSE,
                'type' => 'string',

            ),
            'type' => array(
                'required' => 'true',
                'allowEmpty' => FALSE,
                'type' => 'integer',

            ),
            'title' => array(
                'required' => 'true',
                'allowEmpty' => FALSE,
                'type' => 'string',


            ),
            'status' => array(
                'required' => 'true',
                'allowEmpty' => FALSE,
                'type' => 'integer',
                'default' => 1

            ),

        );
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }

}