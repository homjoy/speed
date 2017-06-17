<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Shop\Coupon;
use Joint\Package\Common\Response;
use Joint\Modules\Common\BaseModule;

/**
 * 我的优惠券
 * Class CouponHomeList
 * @package Joint\Modules\Kefu
 * @author yongzhao
 * @date 2015-10-12
 * @api_wiki http://redmine.meilishuo.com/projects/platformapi/wiki/HOST_couponhome_list
 */
class CouponHomeList extends BaseModule {

    protected $params = array();
    protected $errors = array();

    public function run(){
        $this->_init();
        $result = Coupon::getInstance()->couponHomeList($this->params);

        if ($result === FALSE) {
            $result = Response::gen_error(60001);
        }
        return $this->app->response->setBody($result);
    }

    private function _init(){
        $this->rules = array(
            'status' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'string',
                'default'       => 0,
            ),
            'offset' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'integer',
                'default'       => '',
            ),
            'limit' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'integer',
                'default'       => '',
            ),
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