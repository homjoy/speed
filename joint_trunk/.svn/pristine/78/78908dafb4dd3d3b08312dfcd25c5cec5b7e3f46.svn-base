<?php
namespace Joint\Modules\Itserver;
use Libs\Util\Format;
use Joint\Modules\Common\BaseModule;
use Joint\Package\Common\Response;
use Joint\Package\Utils\VisitorWifi;
/**
 * wifi 登陆陆权限
 * @author hongzhou@meilishuo.com
 * @date 2015-9-02
 */
class VisitorWifiDisable  extends BaseModule{
    protected $errors = NULL;
    private   $params = NULL;
    public function run() {

        $this->_init();

        $ret =  VisitorWifi::disable($this->params['op'], $this->params['id']);
        if (empty($ret)||(isset($ret['code'])&& $ret['code']!=200)) {
            $return = Response::gen_error(30001,'','禁用失败');
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
            'id' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'integer',
                'maxLength'=> 30
            ),

        );

        $this->params = $this->post()->safe();
        $this->errors = $this->post()->getErrors();
        return TRUE;
    }



}