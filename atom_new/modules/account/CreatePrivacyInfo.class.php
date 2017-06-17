<?php
namespace Atom\Modules\Account;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Account\UserPrivacyInfo;

/**
 * CreatePrivacyInfo
 * @author hongzhou@meilishuo.com
 * @since 2015-08-20
 */

class CreatePrivacyInfo extends \Atom\Modules\Common\BaseModule {

    protected $params;


    public function run() {

        $this->_init();
        //校验
        if($this->post()->hasError()){
            $return = Response::gen_error(10001, '', $this->post()->getErrors());
            return $this->app->response->setBody($return);
        }

        if(isset($this->params['contract_start_time']) && !empty($this->params['contract_start_time'])){
            $this->params['contract_start_time'] = date('Y-m-d',strtotime($this->params['contract_start_time']));
        }
        if(isset($this->params['contract_end_time'])&& !empty($this->params['contract_end_time'])){
            $this->params['contract_end_time'] = date('Y-m-d',strtotime($this->params['contract_end_time']));
        }
        if(isset($this->params['children_birthday']) && !empty($this->params['children_birthday'])){
            $this->params['children_birthday'] = date('Y-m-d',strtotime($this->params['children_birthday']));
        }
        if(isset($this->params['update_time'])&&  !empty($this->params['update_time'])){
            $this->params['update_time'] = date('Y-m-d H:i:s',strtotime($this->params['update_time']));
        }
        $result= array();
        if(isset($this->params['user_id'])){
            $result = UserPrivacyInfo::model()->getDataList(array('user_id'=>$this->params['user_id']));
            if(!empty($result)){
                return  $this->app->response->setBody(Response::gen_error(50003));
            }

        }
         //插入
        $result = UserPrivacyInfo::model()->insert($this->params);

        if ($result === FALSE) {
            $return = Response::gen_error(50001);
        }elseif (empty($result)) {
            $return = Response::gen_error(30001);
        }else{
            $return = Response::gen_success($result);
        }

        $this->app->response->setBody($return);
    }

    /**
     * 参数初始化
     */
    protected function _init()
    {
        $data = $this->request->POST;
        $data_check = array(
            'user_id'=>array(
                'required'=> true,
                'allowEmpty'=> false,
                'type'=>'integer',
            ),
            'hukou'=>array(
                'type'=>'string',
                'maxLength' => 30,
            ),
            'education'=>array(
                'type'=>'string',
                'maxLength' => 10,
            ),
            'school'=>array(
                'type'=>'string',
                'maxLength' => 30,
            ),
            'speciality'=>array(
                'type'=>'string',
                'maxLength' => 30,
            ),
            'last_work'=>array(
                'type'=>'string',
                'maxLength' => 30,
            ),
            'emergency_person'=>array(
                'type'=>'string',
                'maxLength' => 30,
            ),
            'emergency_phone'=>array(
                'type'=>'string',
                'maxLength' => 30,
            ),
            'contract_start_time'=>array(
                'type'=>'string',
            ),
            'contract_end_time'=>array(
                'type'=>'string',
            ),
            'id_number'=>array(
                'type'=>'string',
                'maxLength' => 30,
            ),
            'address'=>array(
                'type'=>'string',
                'maxLength' => 50,
            ),
            'personal_mail'=>array(
                'type'=>'string',
                'maxLength' => 30,
            ),
            'married'=>array(
                'type'=>'integer',
                'default' => 0,
            ),
            'marry_time'=>array(
                'type'=>'string',
            ),
            'children_birthday'=>array(
                'type'=>'string',
            ),
            'update_time'=>array(
                'type'=>'string',
         ),

        );
        $data_check     = array_intersect_key($data_check,$data);
        $this->rules    =  $data_check;
        $this->params   = $this->post()->safe();
    }

}
