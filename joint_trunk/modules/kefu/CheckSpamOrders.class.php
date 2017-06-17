<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Common\Response;
use Joint\Package\Order\OrderInfo;

/**
 * Class CheckSpamOrders
 * @package Joint\Modules\Kefu
 * @author yongzhao
 * @desc 检查是否刷单
 * @date 2015-09-25
 */
class CheckSpamOrders extends \Joint\Modules\Common\BaseModule {

    protected $params = array();
    protected $errors = array();

    public function run(){
        $this->_init();


        $data = OrderInfo::getInstance()->checkSpamOrders($this->params);

        if ($data === FALSE) {
            $data = Response::gen_error(60001);
        }

        return $this->app->response->setBody($data);
    }

    private function _init(){
        $allRules = array(
            'order_ids' => array(
                'required'      => true,
                'allowEmpty'    => false,
                'type'          => 'string',
                'default'       => '',
            ),
            'all' => array(
                'required'      => false,
                'allowEmpty'    => true,
                'type'          => 'integer',
                'default'       => '',
            ),
        );
        $request = $this->request->REQUEST;
        $this->rules = array_intersect_key($allRules, $request);

        $this->params  = $this->query()->safe();

    }
}


?>