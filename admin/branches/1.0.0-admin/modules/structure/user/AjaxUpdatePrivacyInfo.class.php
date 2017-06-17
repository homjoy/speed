<?php
namespace Admin\Modules\Structure\User;
use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Account\PrivacyInfo;
use Admin\Package\Log\Log;
/**
 * 添加用户信息
 * @author hongzhou@meilishuo.com
 * @since 2015-9-14 下午12:53:13
 */
class AjaxUpdatePrivacyInfo extends BaseModule {

    protected $errors = NULL;
    private   $params = NULL;
    public static $VIEW_SWITCH_JSON = TRUE;
    const USER_TYPE = 4;
    
    public function run() {

        $this->_init();
        if(empty($this->params['user_id'])|| $this->post()->hasError()){
            $return = Response::gen_error(10001, '', $this->post()->getErrors());
            return $this->app->response->setBody($return);
        }

        $return_data_privacy =$this->updatePrivacy($this->params);

        $this->doLog($this->params);
        if($return_data_privacy ===FALSE ){
           return $this->app->response->setBody(Response::gen_error(50001,'失败'));
        }
        $this->app->response->setBody(Response::gen_success($this->params));

    }

    protected function updatePrivacy($data_privacy){

        $return_data_privacy=FALSE;
         if( count($data_privacy['user_id'])>0){
             $return_data_privacy = PrivacyInfo::getInstance()->updatePrivacyInfo($data_privacy);

          }
        return $return_data_privacy;
    }

    protected function doLog($new_param=array(),$old_param='update'){

        $ret = Log::getInstance()->createLogs(array(
                'user_id'=>$this->user['id'],
                'handle_id'=>isset($new_param['user_id'])?$new_param['user_id']:'',
                'operation_type'=>$old_param,
                'after_data'=>json_encode($new_param),
                'handle_type'=>self::USER_TYPE
            )
        );
        return $ret;
    }


    private function _init() {
        $data = $this->request->POST;
        $data_check  = array(

            'user_id' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'integer',
            ),
             'hukou' => array(
                'type'=>'string',
            ),
            'education' => array(
                'type'=>'string',
            ),
            'school'=> array(
                'type'=>'string',
                'maxLength'=> 30,
            ),
            'speciality'=> array(
                'type'=>'string',
            ),
            'last_work'=> array(
                'type'=>'string',
                'maxLength'=> 50,
            ),
            'emergency_person'=> array(
                'type'=>'string',
            ),
            'emergency_phone'=> array(
                'type'=>'string',
                'maxLength'=> 11,
            ),
            'contract_start_time'=> array(
                'type'=>'string',

                'maxLength'=>30 ,
                'default'=>'0000-00-00'
            ),
            'contract_end_time'=>array(
                'type'=>'string',
                'maxLength'=>30 ,
                'default'=>'0000-00-00'
            ),
            'id_number'=>array(
                'type'=>'string',
                'maxLength'=> 30,
            ),
            'address'=>array(
                'type'=>'string',
            ), 
            'personal_mail'=>array(
                'type'=>'string',
                'maxLength'=> 18,
            ),
            'married'=>array(
                'type'=>'integer',
                'default'=>0,
            ),
            'marry_time'=>array(
                'type'=>'string',
                'default'=>'0000-00-00'
            ),
            'children_birthday'=>array(
                'type'=>'string',
                 'default'=>'0000-00-00'

            )

        );

        $data_check     = array_intersect_key($data_check,$data);
        $this->rules    =  $data_check;
        $this->params   = $this->post()->safe();
    }
    
}