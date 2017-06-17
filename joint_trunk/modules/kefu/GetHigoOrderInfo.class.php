<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Common\Response;
use Joint\Package\Order\HigoOrderInfo;

/**
 * 获取higo的订单信息
 * Class GetHigoOrderInfo
 * @package Joint\Modules\Kefu
 * @author guojiezhu
 */
class GetHigoOrderInfo extends \Joint\Modules\Common\BaseModule {

    protected $params = array();
    protected $errors = array();
    protected $debug = '';

    public function run() {

        $this->_init();
        if($this->debug){ var_dump($this->params); }
        $this->params['debug'] = $this->debug;
        $data = HigoOrderInfo::getInstance()->getOrderInfo($this->params);
        if($this->debug){ var_dump($data); }
        if ($data === FALSE) {
            $data = Response::gen_error(60001);
        }
        return $this->app->response->setBody($data);
    }

    private function _init() {
        $this->debug = isset($this->app->request->REQUEST['debug']) ? $this->app->request->REQUEST['debug'] : '';
        $allRules = array(
            'order_id' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'string',
                'default'       => 0,
            ),
            'buyer_account_mobile' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'string',
                'default'       => 0,
            ),
            'receiver_mobile' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'string',
                'default'       => 0,
            ),
            'buyer_nickname' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'string',
                'default'       => 0,
            ),
            'buyer_account_id' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'string',
                'default'       => 0,
            ),
            'shop_account_id' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'string',
                'default'       => 0,
            ),
            'shop_id' => array(
                'required'      => FALSE,
                'allowEmpty'    => TRUE,
                'type'          => 'string',
                'default'       => 0,
            ),
            'page_curr' => array(
               'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'string',
                'default'       => 1,
            ),
            'page_size' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'string',
                'default'       => 20,
            ),
            'ctime_start' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'string',
                'default'       => '',
            ),
            'ctime_end' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'string',
                'default'       => '',
            ),
            
        );
        $request = $this->request->REQUEST;

        $this->rules = array_intersect_key($allRules, $request);
        $this->params = $this->request()->safe();

    }

}

?>