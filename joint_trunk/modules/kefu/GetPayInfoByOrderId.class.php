<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Common\Response;
use Joint\Package\Order\OrderPay;
/**
 * Class GetPayInfoByOrderNo
 * @package Joint\Modules\Kefu
 * @author yongzhao
 * @desc 根据订单号获取支付详情的接口
 */
class GetPayInfoByOrderId extends \Joint\Modules\Common\BaseModule {

    protected $params = array();
    protected $errors = array();

    public function run(){
        $this->_init();


        $data = OrderPay::getInstance()->getPayListByOrderId($this->params);

        if ($data === FALSE) {
            $data = Response::gen_error(60001);
        }

        return $this->app->response->setBody($data);
    }

    private function _init(){
        $allRules = array(
            'order_id' => array(
                'required'      => true,
                'allowEmpty'    => false,
                'type'          => 'string',
                'default'       => '',
            ),
        );
        $request = $this->request->REQUEST;
        $this->rules = array_intersect_key($allRules, $request);

        $this->params  = $this->query()->safe();

    }
}


?>