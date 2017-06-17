<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Order\RefundService;
use Joint\Package\Common\Response;

/**
 * 工单系统处理因商户担保金不足导致的退款进入工单
 * Class RefundWorkOrder
 * @package Joint\Modules\Kefu
 * @author yongzhao
 * @wiki http://redmine.meilishuo.com/projects/doota/wiki/HOSTrefundRefund_work_order
 */
class RefundWorkOrder extends \Joint\Modules\Common\BaseModule {

    protected $params = array();
    protected $errors = array();

    public function run(){
        $this->_init();
        $result = RefundService::getInstance()->dealRefund($this->params);

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
            'type' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'string',
                'default'       => '',
            ),
            'comment' => array(
                'required'      => FALSE,
                'allowEmpty'    => TRUE,
                'type'          => 'string',
                'default'       => 0,
            ),

        );
        $this->params = $this->request()->safe();

    }
}
?>