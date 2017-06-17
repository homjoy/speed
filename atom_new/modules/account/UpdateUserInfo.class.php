<?php
namespace Atom\Modules\Account;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Account\UserInfo;

/**
 * UpdateUserInfo
 * @author hongzhou@meilishuo.com
 * @since 2015-08-20
 */

class UpdateUserInfo extends \Atom\Modules\Common\BaseModule {

    protected $params;
    protected $errors = array();


    public function run() {

        $this->_init();


        if(!isset($this->params['user_id'])||empty($this->params['user_id'])|| (count($this->params)<=1)){
            $return = Response::gen_error(10001, '','没有更新信息或者没有填写部门id');
            return $this->app->response->setBody($return);
        }

        //查询
        $result = UserInfo::model()->updateById($this->params);

        if ($result === FALSE) {
            $return = Response::gen_error(50001);
        }elseif (empty($result)) {
            $return = array();
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
            'name_en' => array(
                'type'=>'string',
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'maxLength'=> 30,
            ),
            'mail' => array(
                'type'=>'string',
                'maxLength'=> 30,
            ),
            'hire_time' => array(
                'type'=>'string',
            ),
            'positive_time' => array(
                'type'=>'string',
            ),
            'staff_id' => array(
                'type'=>'string',
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'maxLength'=> 10,
            ),
            'graduation_time' => array(
                'type'=>'string',
                'required' => TRUE,
                'allowEmpty' => FALSE,
            ),
            'gender' => array(
                'type'=>'integer',
                'enum'=> array(0,1)
            ),
            'status' => array(
                'type'=>'integer',
                'enum'=> array(1,2,3)
            ),
            'flag' => array(
                'type'=>'integer',
                'enum'=> array(1,2,3,4),
                //建议更新字段 假如没有给个1
            ),
            'update_time'=>array(
                'type'=>'datetime',
                'default'=>@date('Y-m-d H:i:s',time()),
            ),
            'direct_leader' => array( //hongzhou edit
                'type'=>'integer',

            )
        );
//数据校验

        $data_check     = array_intersect_key($data_check,$data);
        $this->rules    =  $data_check;
        $this->params   = $this->post()->safe();
    }

}