<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Shop\ShopArbitrate;
use Joint\Package\Common\Response;

/**
 * 风控系统/工单系统更新退款原因
 * Class RefundRiskReason
 * @package Joint\Modules\Kefu
 * @author yongzhao
 * @date 2015-09-29
 * @wiki http://redmine.meilishuo.com/projects/doota/wiki/HOSTrefundrefund_risk_reason
 */
class RefundRiskReason extends \Joint\Modules\Common\BaseModule {

    protected $params = array();
    protected $errors = array();

    public function run(){
        $this->_init();
        $result = ShopArbitrate::getInstance()->updateRefundRiskReason($this->params);

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
            'select_reason' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'string',
                'default'       => '',
            ),
            'select_reason_id' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'integer',
                'default'       => 0,
            ),
            'service_type' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'string',
                'default'       => '',
            ),
        );
        $this->params = $this->request()->safe();

    }
}
?>