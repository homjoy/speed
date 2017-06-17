<?php
namespace Joint\Modules\Itserver;
use Libs\Util\Format;
use Joint\Modules\Common\BaseModule;
use Joint\Package\Common\Response;
use Joint\Package\Utils\Vpn;
/**
 * vpn 登陆陆权限
 * @author hongzhou@meilishuo.com
 * @date 2015-9-02
 */
class VpnEditPwd  extends BaseModule{
    protected $errors = NULL;
    private   $params = NULL;
    public function run() {

        $this->_init();

        $ret = Vpn::setUser($this->params['op'],$this->params['u'] , $this->params['p'], $this->params['enabled']);
        if (empty($ret)|| (isset($ret['code'])&& $ret['code']!=200)) {
            $return = Response::gen_error(30001,'','修改失败');
        }else{
            $return = $ret;
        }
        $this->app->response->setBody($return);
    }


    private function _init() {

        $this->rules = array(
            'op' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'string',
                'maxLength'=> 30
            ),
            'u' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'string',
                'maxLength'=> 30
            ),
            'p' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'string',
                'maxLength'=> 30
            ),
            'enabled' => array(
                'type'=>'integer',
                'default'=> 1,
            ),

        );

        $this->params = $this->post()->safe();
        $this->errors = $this->post()->getErrors();
        return TRUE;
    }
	
	
}