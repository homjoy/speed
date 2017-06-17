<?php

namespace Apicloud\Modules\Auth;

use Apicloud\Package\Common\Response;
use Apicloud\Package\Account\MGJUserInfo;
use Apicloud\Modules\Common\BaseModule;
/**
 * 获取蘑菇街的信息
 * Class MgjUserInfo
 * @package Apicloud\Modules\Auth
 * User: guojiezhu
 * Date: 2016-03-30
 */

class GetMgjUserInfo extends BaseModule
{

    protected $params = array();
    protected $errors = array();


    public function run()
    {
        $this->_init();
        $query_params = array();
        if(!empty($this->params)){
            $query_params = array(
                'workId' => $this->params['user_id']
            );
        }
        $mgj_user_info = MGJUserInfo::getInstance()->getUserInfo($query_params);

        $this->app->response->setBody(Response::gen_success($mgj_user_info));
        //$this->app->log->log('loginMsg', $return);
    }


    private function _init()
    {
        $this->rules = array(
            'user_id' => array(
                'required' => true,
                'type' => 'string',
            ),



        );
        $this->params   = $this->query()->safe();
        $this->errors   = $this->query()->getErrors();

    }

}
