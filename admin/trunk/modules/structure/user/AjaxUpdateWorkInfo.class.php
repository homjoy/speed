<?php
namespace Admin\Modules\Structure\User;
use Admin\Package\Account\WorkInfo;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Log\Log;
/**
 * 添加用户信息
 * @author hongzhou@meilishuo.com
 * @since 2015-9-14 下午12:53:13
 */
class AjaxUpdateWorkInfo extends BaseModule {

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

        $return_data_work = $this->updateWork($this->params);

        if($return_data_work ===FALSE ){
           return $this->app->response->setBody(Response::gen_error(50001,'失败'));
        }
        $this->doLog($this->params);
        $this->app->response->setBody(Response::gen_success($this->params));

    }
    protected function updateWork($data_work){

        $return_data_work=FALSE;
        if(isset($data_work['others_work'])){
            $data_work['others'] =$data_work['others_work'];
            unset($data_work['others_work']);
        }
        if( count($data_work)>1){

            $return_data_work = WorkInfo::getInstance()->updateWorkInfo($data_work);

        }
        return $return_data_work;


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
//user_work_info
          'position' => array(
              'type'=>'string',
              'maxLength'=> 30,
          ),
          'redmineid' => array(
              'type'=>'integer',
          ),
            'mls_id' => array(
                'type'=>'integer',
            ),
          'mls_nickname' => array(
              'type'=>'string',
              'maxLength'=> 50,
          ),
            'others_work' => array(
                'type'=>'string',
                'maxLength'=> 300,
            ),
            'business_company_id' => array(
                'type'=>'integer',
                'default' =>0,
            ),
            'contract_company_id' => array(
                'type'=>'integer',
                'default' =>0,
            ),
            'job_level_id' => array(
                'type'=>'integer',
                'default' =>0,
            ),
            'job_title_id' => array(
                'type'=>'integer',
                'default' =>0,
            ),
            'work_city' => array(
                'type'=>'string',
                'maxLength'=> 10,
            ),

        );

        $data_check     = array_intersect_key($data_check,$data);
        $this->rules    =  $data_check;
        $this->params   = $this->post()->safe();
    }
    
}