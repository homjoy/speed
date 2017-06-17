<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Order\RefundService;
use Joint\Package\Common\Response;

/**
 * 客服确认收货并退款(新版) 和 商家拒绝退款
 * Class RefundServiceGoods
 * @package Joint\Modules\Kefu
 * @author yongzhao
 * @date 2015-10-12
 * @wiki http://redmine.meilishuo.com/projects/doota/wiki/HOSTrefundrefund_service_goods
 */
class RefundServiceGoods extends \Joint\Modules\Common\BaseModule {

    protected $params = array();
    protected $errors = array();

    public function run(){
        $this->_init();
        $result = RefundService::getInstance()->refundServiceGoods($this->params);

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
                'type'          => 'integer',
                'default'       => '',
            ),
            'comment' => array(
                'required'      => FALSE,
                'allowEmpty'    => TRUE,
                'type'          => 'integer',
                'default'       => '',
            ),
            'admin_uid' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'integer',
                'default'       => '',
            ),
            'admin_name' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'string',
                'default'       => '',
            ),
            'token' => array(
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