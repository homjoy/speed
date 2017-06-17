<?php
namespace Atom\Modules\Account;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Account\UserPrivacyInfo;

/**
 * UpadatePrivacyInfo
 * @author hongzhou@meilishuo.com
 * @since 2015-08-20
 */

class UpdatePrivacyInfo extends \Atom\Modules\Common\BaseModule {

    protected $params;

    public function run() {

        $this->_init();

//对数组过滤
        if(!isset($this->params['user_id'])||empty($this->params['user_id'])|| (count($this->params)<=1)){
            $return = Response::gen_error(10001, '','没有更新信息或者没有填写部门id');
            return $this->app->response->setBody($return);
        }
        
        //查询
        $result = UserPrivacyInfo::model()->updateById($this->params);

        if ($result === FALSE) {
            $return = Response::gen_error(50001);
        }elseif (empty($result)) {
            $return = array();
        }else{
            $return = Response::gen_success($result);
        }

        $this->app->response->setBody($return);
    }
    /*
    *filter
    */

    /**
     * 参数初始化
     */
    protected function _init()
    {
        $data = $this->request->POST;
        $data_check = array(
            'user_id' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
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
            ),
            'marry_time'=>array(
                'type'=>'string',
            ),
            'children_birthday'=>array(
                'type'=>'string',
            ),
            'update_time'=>array(
                'type'=>'datetime',
                'default'=>@date('Y-m-d H:i:s',time()),
            ),
        );
//数据校验
        $data_check     = array_intersect_key($data_check,$data);
        $this->rules    =  $data_check;
        $this->params   = $this->post()->safe();
    }

}
