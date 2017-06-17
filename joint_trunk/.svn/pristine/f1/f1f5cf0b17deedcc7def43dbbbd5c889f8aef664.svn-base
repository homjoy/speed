<?php
namespace Joint\Modules\Outsourcing;

use Joint\Modules\Common\BaseModule;
use Joint\Package\Common\Response;
use Frame\Speed\Lib\Api;

/**
 * Email 数据创建
 * Class EmailCreate
 *
 * @package Joint\Modules\OutsourcingUserCreate
 */
class OutsourcingUserCreate  extends BaseModule{
    protected $errors = NULL;
    private   $params = NULL;

    public function run() {

        $this->_init();
        //创建用户
        if(!empty($this->params['mail'])){
            $mail_array = explode('@',$this->params['mail']);
            $this->params['mail'] = isset($mail_array[0] ) ? $mail_array[0] :'';
            $this->params['mail_suffix'] = isset($mail_array[1]) ? $mail_array[1] : '';
        }
        if(empty($this->params['mail']) || empty($this->params['name_cn'])){
            $return = Response::getInstance()->gen_error(50001,'不能为空');
            return  $this->app->response->setBody($return);
        }

        //查询是否存在
        $this->params['type'] = 1;

        $query_user_info['status'] = array(1,2,3);
        $query_user_info['type'] = 1;
        $query_user_info['mail'] = $this->params['mail'];
        $out_user_info = Api::atom('account/get_user_outsourcing_info', $query_user_info);

        if(!empty($out_user_info)){
            $return = Response::getInstance()->gen_error(60001,'邮箱'.$this->params['mail'].'@'
            .$this->params['mail_suffix'].'已经存在,请使用其他的邮箱注册');
            return $this->app->response->setBody($return);
        }
        $result = Api::atom('account/create_user_outsourcing_info ', $this->params);
        if(intval($result) >0){
            $return = Response::getInstance()->gen_success('创建成功');
        }else{
            $return = Response::getInstance()->gen_error(50001,'创建失败');
        }
        return  $this->app->response->setBody($return);
    }

    /**
     * 获取参数
     * @return bool
     */
    private function _init() {

        $this->rules = array(
            'mail' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'string',

            ),
            'name_cn' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'string',
                'maxLength'=> 30
            ),

        );

        $this->params = $this->post()->safe();
        $this->errors = $this->post()->getErrors();
        return TRUE;
    }



}
