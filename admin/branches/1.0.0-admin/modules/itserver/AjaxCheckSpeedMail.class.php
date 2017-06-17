<?php
namespace Admin\Modules\Itserver;
use Admin\Package\Itserver\Itserver;
use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Account\UserInfo;

/**
 *为独立创建speed账号使用
 * hongzhou@meilishuo.com
 * 2015-12-08
 */

class AjaxCheckSpeedMail extends  BaseModule {
    protected $errors = NULL;
    private   $params = NULL;
    public static $VIEW_SWITCH_JSON = TRUE;
    public function run() {
       $this->_init();

        if($this->post()->hasError()){
            $return = Response::gen_error(10001, '', $this->post()->getErrors());
            return $this->app->response->setBody($return);
        }
       $speed  = $this->speedMail(
           array(
               'mail'=>$this->params['param'],
               'status'=>$this->params['status']
           )
       );
       if($speed){
           $result = Response::gen_error(50001,'邮箱不可用请替换');
           return $this->app->response->setBody($result);
       }
       $office_mail  = Itserver::getInstance()->getOfficialMail(
           array(
               'mail_name'=>$this->params['param'],
               'status'=>$this->params['status']
           )
       );
        if($office_mail){
            $result = Response::gen_error(50001,'邮箱不可用请替换');
            return $this->app->response->setBody($result);
        }
        $it = Itserver::getInstance()->checkMail(
            array(
                'email'=>$this->params['param'].'@meilishuo.com',
                'depart_id'=>$this->params['depart_id']
            )
        );
        if(!$it){
            $it = Response::gen_success('邮箱SPEED可注册,邮箱系统已注册,请找IT核对');
            return $this->app->response->setBody($it);
        }
        $this->app->response->setBody(Response::gen_success('通过信息验证!'));
    }
    /*
     * speed自身检测邮箱是否创建
     * $params
     * 邮箱前缀
     */
    public  function speedMail($params=array()){

        $info = UserInfo::getInstance()->getUserInfo($params);
        return $info;

    }
    /*
 * speed自身检测邮箱是否创建
 * $params
 * 邮箱前缀
 */
    public  function speedOfficeMail($params=array()){

        $info = UserInfo::getInstance()->getUserInfo($params);
        return $info;

    }
    private function _init() {

        $this->rules  = array(

//            'name' => array(
//                'required' => TRUE,j
//                'allowEmpty' => FALSE,
//                'type'=>'string',
//            ),
            'param' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'string',
            ),
            'depart_id' => array(//校验时
                 'type'=>'integer',
                 'default'=>10000,
            ),
            'status' => array(//校验时
                'type'=>'multiId',
                'default'=>array(1,2,3),
            )
        );

        $this->params   = $this->post()->safe();
        $this->errors   = $this->post()->getErrors();

    }
}
