<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Order\RefundService;
use Joint\Package\Common\Response;

/**
 * 根据退款单获取可修改的退款原因
 * Class GetRefundReason
 * @package Joint\Modules\Kefu
 * @author yongzhao
 * @wiki http://redmine.meilishuo.com/projects/doota/wiki/HOSTrefundrefund_reason
 */
class GetRefundReason extends \Joint\Modules\Common\BaseModule {

    protected $params = array();
    protected $errors = array();

    public function run(){
        $this->_init();
        $result = RefundService::getInstance()->refundReason($this->params);

        if ($result === FALSE) {
            $result = Response::gen_error(60001);
        }
        return $this->app->response->setBody($result);
    }

    private function _init(){
        $this->rules = array(
            'refund_id' => array(
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