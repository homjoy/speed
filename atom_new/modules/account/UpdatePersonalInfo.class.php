<?php
namespace Atom\Modules\Account;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Account\UserPersonalInfo;

/**
 * UpDatePersonalInfo
 * @author hongzhou@meilishuo.com
 * @since 2015-08-20
 */

class UpdatePersonalInfo extends \Atom\Modules\Common\BaseModule {

    protected $params;

    public function run() {

        $this->_init();
        if(!isset($this->params['user_id'])||empty($this->params['user_id'])|| (count($this->params)<=1)){
            $return = Response::gen_error(10001, '','没有更新信息或者没有填写部门id');
            return $this->app->response->setBody($return);
        }
        //查询
        $result = UserPersonalInfo::model()->updateById($this->params);

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
        $data_check = array(//user_personal_info
            'user_id' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'integer',
            ),
            'nation' => array(
                'type'=>'string',
                'maxLength'=> 30,
            ),
            'birthday' => array(
                'type'=>'string',
            ),
            'mobile' => array(
                'type'=>'string',
                'maxLength'=> 30,
            ),
            'mobile_another' => array(
                'type'=>'string',
                'maxLength'=> 30,
            ),
            'telephone' => array(
                'type'=>'string',
                'maxLength'=> 30,
            ),
            'qq' => array(
                'type'=>'string',
                'maxLength'=> 15,
            ),
            'others' => array(
                'type'=>'string',
                'maxLength'=> 300,
            ),
            'coat_size' => array(
                'type'=>'string',
                'maxLength'=> 5,
            ),
            'pants_size' => array(
                'type'=>'string',
                'maxLength'=> 5,
            ),
            'shoes_size' => array(
                'type'=>'string',
                'maxLength'=> 5,
            ),
            'update_time'=>array(
                'type'=>'datetime',
                'default'=>@date('Y-m-d H:i:s',time()),
            ),
            'coat_color' => array(
                'type'=>'string',
                'maxLength'=> 5,
            ),

        );
//数据校验
        $data_check     = array_intersect_key($data_check,$data);
        $this->rules    =  $data_check;
        $this->params   = $this->post()->safe();

    }

}
