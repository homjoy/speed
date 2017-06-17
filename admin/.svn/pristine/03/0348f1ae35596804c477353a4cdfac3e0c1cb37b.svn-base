<?php
namespace Admin\Modules\Stationery;
use Admin\Package\Core\Config;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Stationery\Stationery;



/**
 * 主页信息
 * @author hongzhou@meilishuo.com
 * @since 2015-8-10 下午12:53:13
 */
class AjaxGet extends BaseModule {

    protected $errors = NULL;
    private   $params = NULL;
    public static $VIEW_SWITCH_JSON = TRUE;
    public function run() {

       $this->_init();

       $return = Stationery::getInstance()->getOfficeSupply($this->params);
       $this->app->response->setBody(Response::gen_success($return));

    }

    private function _init() {

        $this->rules = array(
            'id'  => array(
                'type'    => 'multiId',
                'required' => TRUE,
                'allowEmpty' => FALSE,
            ),
        );
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }

}