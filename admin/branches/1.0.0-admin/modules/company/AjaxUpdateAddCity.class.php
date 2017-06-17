<?php
namespace Admin\Modules\Company;

use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Company\City;
use Admin\Package\Log\Log;
/**
 *  AjaxUpdateAdd
 * hongzhou@meilishuo.com
 * 2016-03-16
 */
class AjaxUpdateAddCity extends BaseModule {

    public static $CITY_COMPANY= 13;
    protected $params = array();
    protected $errors = array();
    public static $VIEW_SWITCH_JSON = TRUE;




    public function run(){
        $this->_init();
        if(empty( $this->params['city_id'] )){
            //添加
            $result = City::getInstance()->cityCreate($this->params );
            //记录logs
            $this->doLog($this->params,'add');
        }else{
            //更新
            if(empty($this->params['city_name'])){
                unset($this->params['city_name']);
            }
            $result = City::getInstance()->cityUpdate($this->params );
            $this->doLog($this->params,'update');
        }

        if ($result === FALSE) {
            $return = Response::gen_error(50001);
        }else{
            $return = Response::gen_success('操作成功');
        }


        $this->app->response->setBody($return);
    }

    protected function doLog($new_param=array(),$old_param ='add'){

        $ret = Log::getInstance()->createLogs(array('user_id'=>$this->user['id'],'handle_id'=>isset($new_param['city_id'])?$new_param['city_id']:100000,
            'operation_type'=>$old_param,'after_data'=>json_encode($new_param),'handle_type'=>self::$CITY_COMPANY));

        return $ret;
    }
    public function _init(){
            $this->rules = array(
                'city_id' => array(
                    'type' => 'integer'
                ),

                'city_name' => array(
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