<?php
namespace Joint\Modules\Releaseauth;
use Libs\Util\Format;
use Joint\Modules\Common\BaseModule;
use Joint\Package\Common\Response;
use Joint\Package\Utils\Wifi;
/**
 * Vpn权限回收
 * @author hongzhou@meilishuo.com
 * @since 2015-8-13 下午4:53:13
 */
class WifiAuthRecover extends BaseModule {

    protected $errors = NULL;
    private   $params = NULL;

    public function run() {

        $this->_init();
        //参数处理
        if ($this->post()->hasError()) {
            $return = Response::gen_error(10001, '', $this->errors);
            return $this->app->response->setBody($return);
        }
        $is_mail = preg_match('/@/', $this->params['mail']);
        if ($is_mail) { // 邮箱
            $is_mail = explode("@",$this->params['mail']);
            $is_mail = $is_mail[0];;
        }else{
            $is_mail =  $this->params['mail'];
        }//得到前缀 拼接curl
        $is_user_mail = preg_match('/@/', $this->params['user_mail']);
        if ($is_user_mail) { // 邮箱
            $is_user_mail = explode("@",$this->params['user_mail']);
            $is_user_mail = $is_user_mail[0];
        }else{
            $is_user_mail =  $this->params['user_mail'];
        }
        $return = Wifi::remove($is_user_mail,$is_mail);
        if(isset($return['code'])&&$return['code']==200){
            return    $this->app->response->setBody(Response::gen_success(array('msg'=>'删除成功')));
        }
        $this->app->response->setBody(Response::gen_error(50001,'','删除失败'));
    }


    private function _init() {

        $this->rules = array(
            'mail' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'string',
                'maxLength'=> 30
            ),
            'user_mail' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'string',
                'maxLength'=> 30
            )
        );

        $this->params = $this->post()->safe();
        $this->errors = $this->post()->getErrors();
        return TRUE;
    }

}