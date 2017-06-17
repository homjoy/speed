<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Shop\Coupon;
use Joint\Package\Common\Response;

/**
 * Class QueryActiveCouponUsage
 * @package Joint\Modules\Kefu
 * @author yongzhao
 * @desc 查询正在使用的优惠券使用信息
 * @wiki http://redmine.meilishuo.com/projects/platformapi/wiki/HOST_couponquery_active_coupon_usage
 */
class QueryActiveCouponUsage extends \Joint\Modules\Common\BaseModule {

    protected $params = array();
    protected $errors = array();

    public function run(){
        $this->_init();
        $result = Coupon::getInstance()->queryActiveCoupon($this->params);

        if ($result === FALSE) {
            $result = Response::gen_error(60001);
        }
        return $this->app->response->setBody($result);
    }

    private function _init(){
        $this->rules = array(
            'coupon_usage_id' => array(
                'required'      => FALSE,
                'allowEmpty'    => TRUE,
                'type'          => 'integer',
                'default'       => 0,
            ),
            'order_id' => array(
                'required'      => FALSE,
                'allowEmpty'    => TRUE,
                'type'          => 'integer',
                'default'       => 0,
            ),
            'mid' => array(
                'required'      => FALSE,
                'allowEmpty'    => TRUE,
                'type'          => 'integer',
                'default'       => 0,
            ),
            'coupon_id' => array(
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