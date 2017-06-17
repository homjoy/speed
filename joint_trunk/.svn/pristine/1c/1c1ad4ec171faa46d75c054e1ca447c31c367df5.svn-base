<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Shop\Coupon;
use Joint\Package\Common\Response;
use Joint\Modules\Common\BaseModule;

/**
 * 我的优惠券数量
 * Class CouponHomeListNum
 * @package Joint\Modules\Kefu
 * @author yongzhao
 * @date 2015-10-16
 * @wiki http://interfaces.meiliworks.com/show/interface?id=1113&work_id=20
 */
class CouponHomeListNum extends BaseModule {

    protected $params = array();
    protected $errors = array();

    public function run(){
        $this->_init();
        $result = Coupon::getInstance()->couponHomeListNum($this->params);

        if ($result === FALSE) {
            $result = Response::gen_error(60001);
        }
        return $this->app->response->setBody($result);
    }

    private function _init(){
        $this->rules = array(
            'uid' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'integer',
                'default'       => 0,
            ),
            'status' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'integer',
                'default'       => '',
            ),
            //'all', 'available', 'used', 'expired','unavailable','unexpired'
            'coupon_type' => array(
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