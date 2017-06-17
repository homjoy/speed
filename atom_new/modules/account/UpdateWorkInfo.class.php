<?php
namespace Atom\Modules\Account;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Account\UserWorkInfo;

/**
 * UpdateWorkInfo
 * @author hongzhou@meilishuo.com
 * @since 2015-08-20
 */

class UpdateWorkInfo extends \Atom\Modules\Common\BaseModule {

    protected $params;


    public function run() {

        $this->_init();

        if(!isset($this->params['user_id'])||empty($this->params['user_id'])|| (count($this->params)<=1)){
            $return = Response::gen_error(10001, '','没有更新信息或者没有填写部门id');
            return $this->app->response->setBody($return);
        }

        //修改
        $result = UserWorkInfo::model()->updateById($this->params);

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
            'others' => array(
                'type'=>'string',
                'maxLength'=> 300,
            ),  
            'job_level_id'=> array(
                'type'=>'integer',
                'maxLength'=> 9,
            ) ,
             'job_title_id'=> array(
                'type'=>'integer',
                'maxLength'=> 9,
            ),
            'contract_company_id'=> array(
                'type'=>'integer',
                'maxLength'=> 5,
            ),
            'business_company_id'=> array(
                'type'=>'integer',
               'maxLength'=> 5,
            ),
           'update_time'=>array(
                'type'=>'datetime',
                'default'=>@date('Y-m-d H:i:s',time()),
            ),
            'bank_card_number'=> array(
                'type'=>'integer',
                'maxLength'=> 50,
            ),
            'work_city'=> array(
                'type' => 'string',
            ),
         );
//数据校验
        $data_check     = array_intersect_key($data_check,$data);
        $this->rules    =  $data_check;
        $this->params   = $this->post()->safe();
    }

}
