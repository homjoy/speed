<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Common\Response;
use Joint\Package\Shop\Settlement;

/**
 * Class MpayQueryBalance
 * @package Joint\Modules\Kefu
 * @author yongzhao
 * @desc 查询商户帐户金数额, 风险准备金余额 保证金数额
 * @wiki http://redmine.meilishuo.com/projects/doota/wiki/HOSTmpaympay_query_balance
 */
class MpayQueryBalance extends \Joint\Modules\Common\BaseModule {

    protected $params = array();
    protected $errors = array();

    public function run() {

        $this->_init();
        //获取退款的信息
        $data = Settlement::getInstance()->shopBalance($this->params);
        if ($data === FALSE) {
            $data = Response::gen_error(60001);
        }
        return $this->app->response->setBody($data);
    }

    private function _init() {
        $allRules = array(
            'type' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'string',
                'default'       => '',
            ),
            'shop_id' => array(
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