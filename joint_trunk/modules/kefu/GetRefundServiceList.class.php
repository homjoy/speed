<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Common\Response;
use Joint\Package\Order\VirusOrderInfo;

/**
 * 获取订单的退款的信息
 * Class GetHigoOrderInfo
 * @package Joint\Modules\Kefu
 * @author guojiezhu
 */
class GetRefundServiceList extends \Joint\Modules\Common\BaseModule {

    protected $params = array();
    protected $errors = array();

    public function run() {

        $this->_init();
        //获取退款的信息
        $data = VirusOrderInfo::getInstance()->getRefundServiceList($this->params);
        if ($data === FALSE) {
            $data = Response::gen_error(60001);
        }
        return $this->app->response->setBody($data);
    }

    private function _init() {
        $allRules = array(
            'order_id' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'string',
                'default'       => 0,
            ),
            'pagesize' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'string',
                'default'       => 50,
            ),
           
        );
        $request = $this->request->REQUEST;
        $this->rules = array_intersect_key($allRules, $request);
        $this->params = $this->request()->safe();

    }

}

?>