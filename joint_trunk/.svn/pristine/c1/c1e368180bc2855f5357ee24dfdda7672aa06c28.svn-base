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
class OutsourcingUserDel  extends BaseModule{
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
        if(empty($this->params['mail']) ){
            $return = Response::getInstance()->gen_error(50001,'不能为空');
            return  $this->app->response->setBody($return);
        }

        $this->params['type'] = 1;
        $this->params['status'] = array(1,2,3);
//        var_dump($this->params);exit;
        $out_user_info = Api::atom('account/get_user_outsourcing_info', $this->params);
        if(empty($out_user_info)){
            $return = Response::getInstance()->gen_error(50001,'用户不存在,不能删除');
            return  $this->app->response->setBody($return);
        }
        $out_user = current($out_user_info);
        $out_user_id = isset($out_user['out_user_id'] ) ? intval($out_user['out_user_id'] ) : 0;

        //调用删除接口
        $update_user = array(
            'out_user_id' => $out_user_id,
            'status' => 2
        );
        $is_del = Api::atom('account/update_user_outsourcing_info', $update_user);
        if(intval($is_del) >0){
            $return = Response::getInstance()->gen_success('删除成功');
        }else{
            $return = Response::getInstance()->gen_error(50001,'删除失败');
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


        );

        $this->params = $this->post()->safe();
        $this->errors = $this->post()->getErrors();
        return TRUE;
    }



}
