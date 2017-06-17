<?php
namespace Admin\Modules\Structure\User;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Account\PersonalInfo;
use Admin\Package\Account\UserInfo;
/**
 * AjaxGetPersonalInfo
 * @author hongzhou@meilishuo.com
 * @since 2016-04-07 下午12:53:13
 */
class AjaxGetPersonalInfo extends BaseModule {
    private $params = array();
    protected $errors = array();
    public static $VIEW_SWITCH_JSON = TRUE;


    public function run() {

        $this->_init();

        if(empty($this->params['mail'])){
            $return = Response::gen_error(10001, '', $this->post()->getErrors());
            return $this->app->response->setBody($return);
        }
        $user =UserInfo::getInstance()->getUserInfo($this->params);
        $user = array_column($user,'user_id');
        $personal =  PersonalInfo::getInstance()->getPersonalInfo(
           array(
               'user_id' => array_pop($user)
           )
        );
        $personal = is_array($personal)?array_pop($personal):'';
        if(empty($personal)){
            $personal = Response::gen_error(50001, '为获取到数据');
        }else{
            $personal =  Response::gen_success($personal);
        }
        $this->app->response->setBody($personal);
    }

    private function _init() {
        $this->rules = array(//user_info
            'mail' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'string',
            ),
        );
        $this->params   = $this->post()->safe();
        $this->errors   = $this->post()->getErrors();
    }
}