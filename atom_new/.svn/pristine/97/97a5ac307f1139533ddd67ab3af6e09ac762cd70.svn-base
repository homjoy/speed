<?php
namespace Atom\Modules\Log;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Log\AdminLog;

/**
 * 获取log add by zhuguojie
 * Class GetAdminLog
 *
 * @package Atom\Modules\Log
 */

class GetAdminLog extends \Atom\Modules\Common\BaseModule {

    protected $params;
    protected $errors = array();

    public function run() {
        $this->_init();
        if($this->query()->hasError()){
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }
        //查询
       $queryParams =array();
       if(!empty($this->params['user_id'])){
           $queryParams['user_id'] =$this->params['user_id'];
       }
        if(!empty($this->params['handle_id'])){
            $queryParams['handle_id'] =$this->params['handle_id'];
        }
        if(!empty($this->params['handle_type'])){
            $queryParams['handle_type'] =$this->params['handle_type'];
        }
        if(!empty($this->params['update_time'])){
            $queryParams['update_time'] =@date('Y-m-d H:i:s',strtotime($this->params['update_time']));
        }
        if(!empty($this->params['end_time'])){
            $queryParams['end_time'] =@date('Y-m-d H:i:s',strtotime($this->params['end_time']));
        }
        if(!empty($this->params['count'])){
            $queryParams['count'] = $this->params['count'];
        }
        if(!empty($this->params['operation_type'])){
            $queryParams['operation_type'] = $this->params['operation_type'];
        }
        if(!empty($this->params['after_data'])){
            $queryParams['after_data'] = $this->params['after_data'];
        }
        $result = AdminLog::model()->getDataList($queryParams,$this->params['offset'],$this->params['limit']);

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
                'type'=>'multiId',
            ),
            'handle_id'=>array(
                'type'=>'multiId',
            ),
            'handle_type'=>array(
                'type'=>'multiId',
            ),
            'update_time'=>array(
                'type'=>'string',
            ),
            'end_time'=>array(
                'type'=>'string',
            ),
            'all'=>array(
                'type'=>'integer',
            ),
            'count' =>array(
                'type'=>'integer',
                'default' => 0

            ),
            'offset' => array(
                'type' => 'integer',
                'default'=>0,
            ),
            'limit' => array(
                'type' => 'integer',
                'default'=>100,
            ),
            'operation_type'=>array(
                'type'=>'string',
            ),
            'after_data'=>array(
                'type'=>'string',
            ),
        );
        $this->params = $this->post()->safe();
        $this->errors = $this->post()->getErrors();
    }

}
