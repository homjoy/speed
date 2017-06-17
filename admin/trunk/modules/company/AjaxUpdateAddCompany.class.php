<?php
namespace Admin\Modules\Company;

use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Company\Company;
/**
 *  AjaxUpdateAdd
 * hongzhou@meilishuo.com
 * 2016-03-16
 */
class AjaxUpdateAddCompany extends BaseModule {


    protected $params = array();
    protected $errors = array();
    public static $VIEW_SWITCH_JSON = TRUE;

    public function run(){
        $this->_init();

        if(empty( $this->params['company_id'] )){
            //添加
            $result = Company::getInstance()->companyCreate($this->params );
        }else{
            //更新
            if(empty($this->params['company_name'])){
                unset($this->params['company_name']);
            }
            $result = Company::getInstance()->companyUpdate($this->params );
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
                'company_id' => array(
                    'type'=>'integer',
                ),
                'city_id' => array(
                    'type'=>'integer',
                ),
                'company_name'=> array(
                    'type'=>'string',
                    'maxLength'=> 30,
                ),
                'company_address'=> array(
                    'type'=>'string',
                    'maxLength'=> 50,
                ),
                'company_addr'=> array(
                    'type'=>'string',
                    'maxLength'=> 30,
                ),
                'telephone'=> array(
                    'type'=>'string',
                    'maxLength'=> 30,
                ),
                'fax'=> array(
                    'type'=>'string',
                    'maxLength'=> 30,
                ),
                'status'=>array(
                    'type'=>'integer',
                    'default'=>1,
                ),

             );
        $this->params = $this->post()->safe();
        $this->errors = $this->post()->getErrors();
    }


}