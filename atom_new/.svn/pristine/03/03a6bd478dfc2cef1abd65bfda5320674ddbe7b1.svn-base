<?php
namespace Atom\Modules\Account;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Account\UserPersonalInfo ;

/**
 * CreatePersonalInfo
 * @author hongzhou@meilishuo.com
 * @since 2015-08-20
 */

class CreatePersonalInfo extends \Atom\Modules\Common\BaseModule {

    protected $params;

    public function run() {

        $this->_init();
        //校验
        if($this->post()->hasError()){
            $return = Response::gen_error(10001, '', $this->post()->getErrors());
            return $this->app->response->setBody($return);
        }
        if(isset($this->params['update_time'])&&  !empty($this->params['update_time']) ){
            $this->params['update_time'] = date('Y-m-d H:i:s',strtotime($this->params['update_time']));
        }
        $result= array();
        if(isset($this->params['user_id'])){
            $result = UserPersonalInfo::model()->getDataList(array('user_id'=>$this->params['user_id']));
            if(!empty($result)){
                return  $this->app->response->setBody(Response::gen_error(50003));
            }
        }
        //插入
        $result = UserPersonalInfo::model()->insert($this->params);

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
            'birthday' => array(//需要传过来
                'type'=>'string',
            ),
            'mobile' => array(//需要传过来
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
            'nation' => array(
             'type'=>'string',
                'maxLength'=> 30,
            ),
            'qq' => array(
                'type'=>'string',
                'maxLength'=> 15,
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
            'others' => array(
                'type'=>'string',
                'maxLength'=> 300,
            ),
            'update_time'=>array(
                'type'=>'string',
           ),
            'coat_color' => array(
                'type'=>'string',
                'maxLength'=> 5,
            ),

        );
        $data_check     = array_intersect_key($data_check,$data);
        $this->rules    =  $data_check;
        $this->params   = $this->post()->safe();
    }

}
