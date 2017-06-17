<?php

namespace Admin\Modules\Hr\Attendance;

use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Attendance\WorkingHours;

/**
 * Created by guojiezhu@meilishuo.com
 * User: MLS  PersonalLeave
 * Date: 15/11/13
 * Time: 下午12:18
 */
class UpdateAddGroup extends BaseModule {

    protected $errors = NULL;
    private $params = NULL;
   // protected $checkUserPermission = TRUE;

    public function run() {

        $this->_init();
        //新增还是 更新
        $return = array();

        if ($this->params['id'] <= 0) {
            //新增
        } else {//更改
            $working_hours = WorkingHours::getInstance()->getWorkingHours($this->params);
            $return['data'] = current($working_hours);
        }
       
       
        return $this->app->response->setBody($return);
    }

    private function _init() {
        $this->rules = array(
            'id' => array(
                'type' => 'integer',
                'default' => 0,
            )
        );
        $this->params = $this->request()->safe();
        $this->errors = $this->request()->getErrors();
    }

}
