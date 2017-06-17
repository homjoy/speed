<?php
namespace Admin\Modules\Hr\Leave;
use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Hr_leave\LeaveList;
use Admin\Package\Account\UserInfo;
/**
 * Created by hongzhou@meilishuo.com
 * User: MLS  LeaveUpdate
 * Date: 15/11/25
 * Time: 下午12:18
 */
class LeaveUpdate extends BaseModule {

    protected $params = array();
    protected $errors = array();
//    public static $VIEW_SWITCH_JSON = TRUE;
    public function run() {
        $this->_init();

        if($this->query()->hasError()){
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }
        $result =LeaveList::getInstance()->getLeaveList($this->params);
        $result =is_array($result)?array_pop($result):'';

        if ($result === FALSE) {
            $return = Response::gen_error(50001,'获取信息失败');
        }else{
            $return = Response::gen_success($result);
        }
        return $this->app->response->setBody($return);
    }

    /**
     * 参数初始化
     */
    protected function _init(){

        $this->rules  = array(
            'order_id' => array(
                'required'	=> true,
                'type'	=> 'integer',
            )
        );
//数据校验
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }
}
