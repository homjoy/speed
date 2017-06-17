<?php
namespace Atom\Modules\Log;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Log\AdminLog;

/**
 * 创建log 获取log add by zhuguojie
 * Class CreateAdminLog
 *
 * @package Atom\Modules\Log
 */

class CreateAdminLog extends \Atom\Modules\Common\BaseModule {

    protected $params;
    protected $errors = array();

    public function run() {
        $this->_init();

        if($this->post()->hasError()){
            $return = Response::gen_error(10001, '', $this->post()->getErrors());
            return $this->app->response->setBody($return);
        }

        //查询

        $after_data=base64_decode(  $this->params['after_data'] ) ;
        //$after_data = json_encode($after_data);
        $this->params['after_data'] = $after_data;//htmlspecialchars_decode(  $this->params['after_data'] ,ENT_QUOTES);
        $result = AdminLog::model()->insert($this->params);

        if ($result<=0) {
            $return = Response::gen_error(50001);
        }elseif (empty($result)) {
            $return = Response::gen_error(30001);
        }else{
            $return = Response::gen_success($result);
        }
        return $this->app->response->setBody($return);


    }

    /**
     * 参数初始化
     */
    protected function _init()
    {
        $this->rules = array(
            'user_id'=>array(
                'required'=>true,
                'allowEmpty'=> false,
                'type'=>'integer',
            ),
            'handle_id'=>array(
                'required'=>true,
                'allowEmpty'=> false,
                'type'=>'integer',
            ),
            'handle_type'=>array(
                'required'=>true,
                'allowEmpty'=> false,
                'type'=>'integer',
            ),
            'before_data'=>array(
                'type'=>'string',
            ),
            'after_data'=>array(
                'required'=>true,
                'allowEmpty'=> false,
                'type'=>'string',
            ),
            'create_reason'=>array(
                'type'=>'string',
            ),
            'name'=>array(
                'type'=>'string',
            ),
            'operation_type'=>array(
                'type'=>'string',
            ),
        );
 	
        $this->params = $this->post()->safe();
        $this->errors = $this->post()->getErrors();
    }

}
