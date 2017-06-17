<?php
namespace Atom\Modules\Account;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Account\UserOutsourcingInfo;

/**
 * 外包用户信息
 * CreateUseKfInfo
 * @author guojiezhu@meilishuo.com
 * @since 2015-12-07
 */

class CreateUserOutsourcingInfo extends \Atom\Modules\Common\BaseModule {

    protected $params;
    private $sample;

    public function run() {

        $this->_init();
        $this->sample = UserOutsourcingInfo::model()->getFields();
        //校验
        if($this->post()->hasError()){
            $return = Response::gen_error(10001, '', $this->post()->getErrors());
            return $this->app->response->setBody($return);
        }
        if(isset($this->params['hire_time'])&& !empty($this->params['hire_time'])){
            $this->params['hire_time'] = date('Y-m-d',strtotime($this->params['hire_time']));
        }
        if(isset($this->params['positive_time'])&& !empty($this->params['positive_time'])){
            $this->params['positive_time'] = date('Y-m-d',strtotime($this->params['positive_time']));
        }
        if(isset($this->params['graduation_time'])&& !empty($this->params['graduation_time'])){
            $this->params['graduation_time'] = date('Y-m-d',strtotime($this->params['graduation_time']));
        }
        if(isset($this->params['update_time'])&& !empty($this->params['update_time'])){
            $this->params['update_time'] = date('Y-m-d H:i:s',strtotime($this->params['update_time']));
        }
        //校验
        $result= array();
        if(isset($this->params['staff_id'])){

            $result = substr($this->params['staff_id'],1,strlen($this->params['staff_id']));
            $result = UserOutsourcingInfo::model()->getDataList(array('staff_id'=>$result,'match'=>'like'),0,100);
            if(!empty($result)){
                return  $this->app->response->setBody(Response::gen_error(50003));
            }
        }
        //插入

        $result = UserOutsourcingInfo::model()->insert($this->params);
        if ($result === FALSE) {
            $return = Response::gen_error(50001);
        }elseif (empty($result)) {
            $return = Response::gen_error(30001);
        }else{
            $this->params['user_id']=$result;
            $return = Response::gen_success(Format::outputData( $this->params, $this->sample));
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
            'depart_id' => array(
                'type'=>'integer',
                'maxLength'=> 9,
                'default' => 0
            ),
            'job_role_id' => array(
                'type'=>'integer',
                'maxLength'=> 9,
            ),
            'name_cn' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'string',
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
            'mail_suffix' => array(
                'type'=>'string',
                'default' =>''
            ),
            'hire_time' => array(
                'type'=>'string',
            ),
            'positive_time' => array(
                'type'=>'string',
            ),
            'staff_id' => array(
                'type'=>'string',
                'maxLength'=> 10,
            ),
            'gender' => array(
                'type'=>'integer',
                'enum'=> array(0,1)
            ),
            'flag' => array(
                'type'=>'integer',
                'enum'=> array(1,2,3,4)//标记:1实习2试用3正式4申请离职
            ),
            'status' => array(
                'type'=>'integer',
                'enum'=> array(1,2,3)
            ),
            'graduation_time' => array(
                'type'=>'string',
            ),
            'update_time'=>array(
               'type'=>'string',
           ),
            'direct_leader' => array( //hongzhou edit
                'type'=>'integer',

            ),
            'type' => array( // 外包人员的类型
                'type'=>'integer',
            ),

        );
        $data_check     = array_intersect_key($data_check,$data);
        $this->rules    =  $data_check;
        $this->params   = $this->post()->safe();
    }

}
