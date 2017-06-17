<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Shop\ShopArbitrate;
use Joint\Package\Common\Response;

/**
 * 商家仲裁详情
 * Class AppealShopDetail
 * @package Joint\Modules\Kefu
 * @author yongzhao
 * @date 2015-09-30
 * @wiki http://redmine.meilishuo.com/projects/doota/wiki/HOSTappealAppeal_shop_detail#HOSTappealAppeal-shop-detail
 */
class AppealShopDetail extends \Joint\Modules\Common\BaseModule {

    protected $params = array();
    protected $errors = array();

    public function run(){
        $this->_init();
        $result = ShopArbitrate::getInstance()->getAppealShopDetail($this->params);

        if ($result === FALSE) {
            $result = Response::gen_error(60001);
        }
        return $this->app->response->setBody($result);
    }

    private function _init(){
        $this->rules = array(
            'user_id' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'integer',
                'default'       => 0,
            ),
            'shop_id' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'integer',
                'default'       => 0,
            ),
            'refund_id' => array(
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