<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Common\Response;
use Joint\Package\Shop\ShopInfo;

/**
 * 获取cs等级、ka商家、白名单、店铺实拍
 * Class ReadShopType
 * @package Joint\Modules\Kefu
 * @author yongzhao
 * @date 2015-12-21
 */
class ReadShopType extends \Joint\Modules\Common\BaseModule {

    protected $params = array();
    protected $errors = array();

    public function run(){
        $this->_init();


        $data = ShopInfo::getInstance()->readShopType($this->params);

        if ($data === FALSE) {
            $data = Response::gen_error(60001);
        }

        return $this->app->response->setBody($data);
    }

    private function _init(){
        $allRules = array(
            'shop_id' => array(
                'required'      => true,
                'allowEmpty'    => false,
                'type'          => 'integer',
                'default'       => 0,
            ),
        );
        $request = $this->request->REQUEST;
        $this->rules = array_intersect_key($allRules, $request);

        $this->params  = $this->query()->safe();

    }
}


?>