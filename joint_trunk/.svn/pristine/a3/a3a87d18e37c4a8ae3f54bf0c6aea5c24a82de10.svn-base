<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Order\OrderInfo;
use Joint\Package\Common\Response;

/**
 * 获取订单延期发货记录
 * Class GetOrderExtendRecvLog
 * @package Joint\Modules\Kefu
 * @author yongzhao
 * @data 2015-09-30
 * @wiki http://redmine.meilishuo.com/projects/doota/wiki/HOSTorderOrder_extend_recv_log
 */
class GetOrderExtendRecvLog extends \Joint\Modules\Common\BaseModule {

    protected $params = array();
    protected $errors = array();

    public function run(){
        $this->_init();
        $result = OrderInfo::getInstance()->order_extend_recv_log($this->params);

        if ($result === FALSE) {
            $result = Response::gen_error(60001);
        }
        return $this->app->response->setBody($result);
    }

    private function _init(){
        $this->rules = array(
            'orderId' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'integer',
                'default'       => 0,
            ),
        );
        $this->params = $this->request()->safe();

    }
}
?>