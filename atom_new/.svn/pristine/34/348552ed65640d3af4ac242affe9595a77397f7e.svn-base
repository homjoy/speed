<?php
namespace Atom\Modules\Account;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Account\UserWorkInfo;

/**
 * CreateWorkInfo
 * @author hongzhou@meilishuo.com
 * @since 2015-08-20
 */

class CreateWorkInfo extends \Atom\Modules\Common\BaseModule {

    protected $params;
    public function run() {

        $this->_init();
        //校验
        if($this->post()->hasError()){
            $return = Response::gen_error(10001, '', $this->post()->getErrors());
            return $this->app->response->setBody($return);
        }
        if(isset($this->params['update_time'])&&  !empty($this->params['update_time'])){
            $this->params['update_time'] = date('Y-m-d H:i:s',strtotime($this->params['update_time']));
        }
        $result= array();
        if(isset($this->params['user_id'])){
            $result = UserWorkInfo::model()->getDataList(array('user_id'=>$this->params['user_id']));
            if(!empty($result)){
                return  $this->app->response->setBody(Response::gen_error(50003));
            }

        }
        //插入
        $result = UserWorkInfo::model()->insert($this->params);

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
            'bank_card_number'=> array(
                'type'=>'integer',
                'maxLength'=> 50,
            ),
           'update_time'=>array(
                'type'=>'string',
            ),
            'work_city' => array(
                'type'=>'string',
            )

        );
        $data_check     = array_intersect_key($data_check,$data);
        $this->rules    =  $data_check;
        $this->params   = $this->post()->safe();
    }

}
