<?php
namespace Admin\Modules\Assets;

use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Assets\AssetsCompany;
/**
 *  AjaxUpdateAdd
 * hongzhou@meilishuo.com
 * 2015-09-25
 */
class AjaxUpdateAddCompany extends BaseModule {


    protected $params = array();
    protected $errors = array();
    public static $VIEW_SWITCH_JSON = TRUE;

    public function run(){
        $this->_init();
        if(empty( $this->params['id'] )){
            //添加
            $result = AssetsCompany::getInstance()->createAssetsCompany($this->params );
        }else{
            //更新
            if(empty($this->params['name'])){
                unset($this->params['name']);
            }
            $result = AssetsCompany::getInstance()->updateAssetsCompany($this->params );
        }

        if ($result === FALSE) {
            $return = Response::gen_error(50001);
        }else{
            $return = Response::gen_success('操作成功');
        }

        $this->app->response->setBody($return);
    }

    public function _init(){
            $this->rules = array(
                'id' => array(
                    'type' => 'integer'
                ),

                'name' => array(
                    'type' => 'string',
                ),
                'memo' => array(
                    'type' => 'string',
                ),
                'status' => array(      //1新建2待接收3处理中4完成5驳回6失效
                    'type'	=> 'integer',
                    'default'=>1
                ),

             );
        $this->params = $this->post()->safe();
        $this->errors = $this->post()->getErrors();
    }


}