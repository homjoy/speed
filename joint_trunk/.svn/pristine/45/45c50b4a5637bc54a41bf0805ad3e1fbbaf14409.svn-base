<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Shop\ShopArbitrate;
use Joint\Package\Common\Response;

/**
 * 退款原因仲裁工单处理
 * Class AppealShopArbitrate
 * @package Joint\Modules\Kefu
 * @author yongzhao
 * @date 2015-09-29
 * @wiki http://redmine.meilishuo.com/projects/doota/wiki/HOSTappealappeal_shop_arbitrate
 */
class AppealShopArbitrate extends \Joint\Modules\Common\BaseModule {

    protected $params = array();
    protected $errors = array();

    public function run(){
        $this->_init();
        $result = ShopArbitrate::getInstance()->updateRefundReason($this->params);

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
            'appeal_id' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'integer',
                'default'       => 0,
            ),
            'refund_reason' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'string',
                'default'       => '',
            ),
            'refund_reason_id' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'integer',
                'default'       => 0,
            ),
            'appeal_type' => array(
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