<?php
namespace Admin\Modules\Hr\Working_calendar;

use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Hr_leave\WorkingCalendarList;

/**
 * 日历读取
 * Class ConfigHome
 * @package Admin\Modules\Config
 * @author guojiezhu
 */
class WorkingCalendarHome extends BaseModule
{
    protected $errors = NULL;
    private $params = NULL;
    protected $checkUserPermisssion = TRUE;



    public function run()
    {

        $this->_init();

        //统计页数
        $total_query_params = $this->params;
        $total_query_params['count'] = 1;
        $total_nums = WorkingCalendarList::getInstance()->getPageCalendarInfo($total_query_params);
        $total_nums = intval($total_nums);
        if ($total_nums <= 0) {
            $return = Response::gen_success(array());
            $return['count'] = 0;
            $return['page'] = 1;
            return $this->app->response->setBody($return);
        }
        //分页控制
        if (isset($this->params['page'])) {
            if ($this->params['page'] <= 0) {
                $this->params['page'] = 1;
            }
            $this->params['offset'] = intval($this->params['page'] - 1) * $this->params['limit'];
        }
        $queryParams = $this->params;
        $result = WorkingCalendarList::getInstance()->getPageCalendarInfo($queryParams);
        $return = Response::gen_success($result);

        $return['count'] = ceil($total_nums / $this->params['limit']);
        $return['page'] = $this->params['page'];
        $return['date_type'] = WorkingCalendarList::$date_type;
        $return['work_status'] = WorkingCalendarList::$work_status;
        $this->app->response->setBody($return);
    }

    private function _init()
    {

        $this->rules = array(
            'page' => array(
                'type' => 'integer',
                'default' => 1,
            ),
            'limit' => array(
                'type' => 'integer',
                'default' => 40,
            ),

        );
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }

}