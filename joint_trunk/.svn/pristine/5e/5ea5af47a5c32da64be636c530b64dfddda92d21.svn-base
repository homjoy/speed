<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Order\RefundService;
use Joint\Package\Common\Response;

/**
 * 发货前退款
 * Class RefundServiceReply
 * @package Joint\Modules\Kefu
 * @author yongzhao
 * @date 2015-09-30
 * @wiki http://redmine.meilishuo.com/projects/doota/wiki/HOSTrefundrefund_service_reply
 */
class RefundServiceReply extends \Joint\Modules\Common\BaseModule {

    protected $params = array();
    protected $errors = array();

    public function run(){
        $this->_init();
        $result = RefundService::getInstance()->refundServiceReply($this->params);

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
        );
        $this->params = $this->request()->safe();

    }
}
?>