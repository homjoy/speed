<?php
namespace Admin\Modules\Itserver;
use Admin\Package\Itserver\Itserver;
use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Account\UserInfo;
/**
 *
 * hongzhou@meilishuo.com
 * 2015-12-08
 */

class AjaxCheckMail extends  BaseModule {
    protected $errors = NULL;


    private   $params = NULL;
    public static $VIEW_SWITCH_JSON = TRUE;
    public function run() {
       $this->_init();

        if($this->post()->hasError()){
            $return = Response::gen_error(10001, '', $this->post()->getErrors());
            return $this->app->response->setBody($return);
        }

       $result = Itserver::getInstance()->checkMail(
           array(
            'email'=>$this->params['param'].'@meilishuo.com',
            'depart_id'=>$this->params['depart_id']
            )
       );
        if(!$result){
            $result = Response::gen_error(50001,'邮箱不可用请替换');
            return $this->app->response->setBody($result);
        }
       $speed  = $this->speedMail(
           array(
               'mail'=>$this->params['param'],
               'status'=>$this->params['status']
           )
       );
       $speed = array_filter($speed);
       if(!$speed){
           $result = Response::gen_success('通过信息验证!');
       }else{
           $result = Response::gen_error(50001,'邮箱不可用请替换');
       }
       return $this->app->response->setBody($result);
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
    private function _init() {

        $this->rules  = array(

//            'name' => array(
//                'required' => TRUE,
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