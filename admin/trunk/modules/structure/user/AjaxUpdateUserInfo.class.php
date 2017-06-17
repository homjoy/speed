<?php
namespace Admin\Modules\Structure\User;
use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Account\UserInfo;
use Admin\Package\Log\Log;
/**
 * 添加用户信息
 * @author hongzhou@meilishuo.com
 * @since 2015-9-14 下午12:53:13
 */
class AjaxUpdateUserInfo extends BaseModule {

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
        //中文校验
        if(isset($this->params['name_cn'])&& ord($this->params['name_cn']) <= 122){
            return $this->app->response->setBody(Response::gen_error(30001,'请核对中文名字,必须使用中文'));
        }
        if(isset($this->params['mail']) && (ord($this->params['mail']) <= 57 ||  ord($this->params['mail']) >122)){
            return $this->app->response->setBody(Response::gen_error(30001,'请核对邮箱,必须使用拼音'));
        }
		$return_data = $this->updateUser($this->params);

        if($return_data === FALSE  ){
           return $this->app->response->setBody(Response::gen_error(50001,'失败'));
        }
        $this->doLog($this->params);
	    $this->app->response->setBody(Response::gen_success($this->params));

	}
	protected function updateUser($data){

        $return_data=FALSE;

        if(count($data)>1){
            $return_data = UserInfo::getInstance()->updateUserInfo($data);

         }
        return $return_data;
      
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
		   'depart_id' => array(
				'type'=>'integer',
                'required' => TRUE,
                'allowEmpty' => FALSE,

			),
			'job_role_id' => array(
				'type'=>'integer',
			),
			'name_cn' => array(
				'type'=>'string',
                  'required' => TRUE,
                'allowEmpty' => FALSE,
				'maxLength'=> 30,
			),
			'name_en' => array( //可不填
				'type'=>'string',
				'maxLength'=> 30,
			),
			'mail' => array(
				'required' => TRUE,
                'allowEmpty' => FALSE,
				'type'=>'string',
				'maxLength'=> 30,
			),
			'hire_time' => array(
                 'required' => TRUE,
                'allowEmpty' => FALSE,
				'type'=>'string',
			),
			'positive_time' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
				'type'=>'string',
			),
			'staff_id' => array(
				'type'=>'string',
                  'required' => TRUE,
                'allowEmpty' => FALSE,
				'maxLength'=> 10,
			),
			'gender' => array(
				'type'=>'integer',
				'enum'=> array(0,1)//性别0女1男
			),
			'flag' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
				'type'=>'integer',
				'enum'=> array(1,2,3,4)//标记:1实习2试用3正式4申请离职
			),
			'status' => array(
				'type'=>'integer',
                 'required' => TRUE,
                'allowEmpty' => FALSE,
				'enum'=> array(1,2,3)
			),
			'graduation_time' => array(
				'type'=>'string',
                'required' => TRUE,
                'allowEmpty' => FALSE,
			),
            'direct_leader' => array(
                'type'=>'integer',

            )

		);

        $data_check     = array_intersect_key($data_check,$data);
        $this->rules    =  $data_check;
        $this->params   = $this->post()->safe();
	}
	
}