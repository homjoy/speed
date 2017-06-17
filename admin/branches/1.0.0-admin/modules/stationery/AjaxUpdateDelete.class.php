<?php
namespace Admin\Modules\stationery;
use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Log\Log;
use Admin\Package\Stationery\Stationery;
/**
 * Created by hongzhou@meilishuo.com
 * User: MLS  AjaxLeaveUpdate
 * Date: 15/11/25
 * Time: 下午12:18
 */
class AjaxUpdateDelete extends BaseModule {
    public static $VIEW_SWITCH_JSON = TRUE;
    protected $params = array();
    protected $errors = array();
    public static $OFFICE_TYPE= 7;

    public function run() {
        $this->_init();
        $old =array();
        if(!isset($this->params['id'])){
            $return = Response::gen_error(10001, 'id不可为空');
            return $this->app->response->setBody($return);
        }
        if(
            (isset($this->params['supply_type'])&&empty($this->params['supply_type'])) ||
            (isset($this->params['supply_name'])&&empty($this->params['supply_name'])) ||
            (isset($this->params['order_type'])&&empty($this->params['order_type']))
         ){
            $return = Response::gen_error(10001,'用品名称、用品类型和用品详细分类不可修改为空');
            return $this->app->response->setBody($return);
        }
        $result = Stationery::getInstance()->updateOfficeSupply($this->params);

        if ($result === FALSE) {
            $return = Response::gen_error(50001);
        }else{
            $return = Response::gen_success($result);
        }

        //记录logs
        $this->doLog($this->params);
        $this->app->response->setBody($return);
    }

    protected function doLog($new_param=array(),$old_param='update'){

        $ret = Log::getInstance()->createLogs(array('user_id'=>$this->user['id'],'handle_id'=>isset($new_param['id'])?$new_param['id']:'',
            'operation_type'=>$old_param,'after_data'=>json_encode($new_param),'handle_type'=>self::$OFFICE_TYPE));
        return $ret;
    }
    /**
     * 参数初始化
     */
    protected function _init(){
        $data = $this->request->POST;
        $data_check = array(
            'id' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'	=> 'integer',
            ),
            'supply_type' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'	=> 'integer',
            ),
            'supply_name' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'	=> 'string',
            ),
            'order_type' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'	=> 'integer',
            ),
            'status' => array(
                'type'	=> 'integer',
                'default'	=> 1,
            ),
            'detail' => array(
                'type'	=> 'string',
            ),
            'memo' => array(
                'type'	=> 'string',
            ),

        );
        //数据校验
        $data_check     = array_intersect_key($data_check,$data);
        $this->rules    =  $data_check;
        $this->params   = $this->post()->safe();
    }
}
