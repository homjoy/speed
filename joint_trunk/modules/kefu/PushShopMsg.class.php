<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Shop\ShopInfo;
use Joint\Package\Common\Response;

/**
 * 给商家发送站内信到商家后台
 * Class PushShopMsg
 * @package Joint\Modules\Kefu
 * @author yongzhao
 * @date 2015-10-12
 * @apiwiki http://interfaces.meiliworks.com/show/interface?id=1080
 */
class PushShopMsg extends \Joint\Modules\Common\BaseModule {

    protected $params = array();
    protected $errors = array();

    public function run(){
        $this->_init();
        $result = ShopInfo::getInstance()->pushMsg($this->params);

        if ($result === FALSE) {
            $result = Response::gen_error(60001);
        }
        return $this->app->response->setBody($result);
    }

    private function _init(){
        $this->rules = array(
            'shop_id' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'integer',
                'default'       => 0,
            ),
            'type' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'integer',
                'default'       => '',
            ),
            'title' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'string',
                'default'       => '',
            ),
            'content' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'string',
                'default'       => '',
            ),
        );
        $this->params = $this->request()->safe();

    }
}
?>