<?php
namespace Atom\Modules\Passport;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Api\Passport;

/**
 * ChangePassword
 * @author hepang@meilishuo.com
 * @since 2015-08-24
 */

class ChangePassword extends \Atom\Modules\Common\BaseModule {

    protected $params;
    protected $errors = array();
    private $sample;

    public function run() {

        $this->_init();

        if($this->post()->hasError()){
            $return = Response::gen_error(10001, '', $this->post()->getErrors());
            return $this->app->response->setBody($return);
        }

        //获取参数
        $queryParams = array();
        if ($this->params['email'] !== '') {
            if(strstr($this->params['email'],'@')){
                $this->params['email'] = strtok($this->params['email'], '@');
            }
            $queryParams['email'] = $this->params['email'] . '@meilishuo.com';
        }
        if ($this->params['oldpassword'] !== '') {
            $queryParams['oldpassword'] = $this->params['oldpassword'];
        }
        if ($this->params['newpassword'] !== '') {
            $queryParams['newpassword'] = $this->params['newpassword'];
        }

        if(empty($queryParams)){
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }

        //查询
        $result = Passport::getInstance()->editPassword($queryParams['email'], $queryParams['oldpassword'], $queryParams['newpassword']);

        if ($result === FALSE) {
            $return = Response::gen_error(50001);
        }elseif (empty($result)) {
            $return = Response::gen_error(30001);
        }elseif ($result['code'] !== 200) {
            $return['code'] = 400;
            $return['error_code'] = $result['code'];
            $return['error_msg'] = $result['msg'];
        }else{
            $return = $result;
        }

        $this->app->response->setBody($return);
    }

    /**
     * 参数初始化
     */
    protected function _init()
    {
        $this->rules = array(
            'email'=>array(
                'type'=>'string',
                'required'=>TRUE,
                'default'=>'',
            ),
            'oldpassword'=>array(
                'type'=>'string',
                'required'=>TRUE,
                'default'=>'',
            ),
            'newpassword'=>array(
                'type'=>'string',
                'required'=>TRUE,
                'default'=>'',
            ),
        );
        $this->params   = $this->post()->safe();
        $this->errors   = $this->post()->getErrors();
    }

}
