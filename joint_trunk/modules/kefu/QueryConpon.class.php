<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Shop\Coupon;
use Joint\Package\Common\Response;

/**
 * Class QueryConpon
 * @package Joint\Modules\Kefu
 * @author yongzhao
 * @desc 查询优惠券
 * @wiki http://redmine.meilishuo.com/projects/platformapi/wiki/HOST_couponquery_coupon
 */
class QueryConpon extends \Joint\Modules\Common\BaseModule {

    protected $params = array();
    protected $errors = array();

    public function run(){
        $this->_init();
        $result = Coupon::getInstance()->queryCoupon($this->params);

        if ($result === FALSE) {
            $result = Response::gen_error(60001);
        }
        return $this->app->response->setBody($result);
    }

    private function _init(){
        $this->rules = array(
            'coupon_id' => array(
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