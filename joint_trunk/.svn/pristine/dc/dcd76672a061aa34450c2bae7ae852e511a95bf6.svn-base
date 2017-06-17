<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Common\Response;
use Joint\Package\Order\OrderPay;

/**
 * 获取订单的支付信息的接口
 * Class GetOrderPayInfo
 * @package Joint\Modules\Kefu
 * @author guojiezhu
 */
class GetOrderPayInfo extends \Joint\Modules\Common\BaseModule {

    protected $params = array();
    protected $errors = array();

    public function run() {

        $this->_init();
        
        switch($this->params['type']){
            case 1 :
                $data = OrderPay::getInstance()->getOrderPay($this->params);
                break;
            case 2 :
                $data = OrderPay::getInstance()->getRefundorder($this->params);
                break;
            default :
                $data = FALSE;
                break;
                
        }
        
        if ($data === FALSE) {
            $data = Response::gen_error(60001);
        }
        return $this->app->response->setBody($data);
    }

    private function _init() {
        $allRules = array(
            'pay_id' => array(
                'required' => true,
                'allowEmpty' => false,
                'type' => 'string',
                'default' => '',
            ),
            'type' => array(
                'required' => true,
                'allowEmpty' => false,
                'type' => 'string',
                'default' => 1,
            ),
        );

        $this->rules = $allRules;
        $this->params = $this->request()->safe();
//        if(empty($this->params)){
//             throw new \Frame\Speed\Exception\ParameterException('参数为空');
//        }
    }

}

?>