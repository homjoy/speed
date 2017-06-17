<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Shop\Coupon;
use Joint\Package\Common\Response;

/**
 * Class BatchGetCouponMeta
 * @package Joint\Modules\Kefu
 * @author yongzhao
 * @desc 批量获取优惠券元数据
 * @wiki http://redmine.meilishuo.com/projects/platformapi/wiki/HOST_couponbatch_get_coupon_meta
 */
class BatchGetCouponMeta extends \Joint\Modules\Common\BaseModule {

    protected $params = array();
    protected $errors = array();

    public function run(){
        $this->_init();
        $result = Coupon::getInstance()->couponMeta($this->params);

        if ($result === FALSE) {
            $result = Response::gen_error(60001);
        }
        return $this->app->response->setBody($result);
    }

    private function _init(){
        $this->rules = array(
            'coupon_type' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'integer',
                'default'       => 0,
            ),
            'coupon_meta_id' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'integer',
                'default'       => 0,
            ),
        );
        $this->params = $this->request()->safe();

    }
}
?>