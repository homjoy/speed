<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Order\OrderInfo;
use Joint\Package\Common\Response;

/**
 * 订单黑名单；查询/提交/删除
 * Class OrderStatus
 * @package Joint\Modules\Kefu
 * @author yongzhao
 * @wiki http://redmine.meilishuo.com/projects/adapter/wiki/SpamOrder_status
 */
class OrderStatus extends \Joint\Modules\Common\BaseModule {

    protected $params = array();
    protected $errors = array();

    public function run(){
        $this->_init();
        $result = OrderInfo::getInstance()->orderStatus($this->params);

        if ($result === FALSE) {
            $result = Response::gen_error(60001);
        }
        return $this->app->response->setBody($result);
    }

    private function _init(){
        $this->rules = array(
            'type' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'string',
                'default'       => '',
            ),
            'order_id' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'string',
                'default'       => 0,
            ),
            'status' => array(
                'required'      => FALSE,
                'allowEmpty'    => TRUE,
                'type'          => 'integer',
                'default'       => 0,
            ),
        );
        $this->params = $this->request()->safe();

    }
}
?>