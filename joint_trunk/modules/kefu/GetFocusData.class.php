<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Shop\ShopInfo;
use Joint\Package\Common\Response;
use Joint\Modules\Common\BaseModule;

/**
 * 获取店铺focus统计信息
 * Class GetFocusData
 * @package Joint\Modules\Kefu
 * @date 2015-10-14
 * @author yongzhao
 * @api_wiki http://interfaces.meiliworks.com/show/interface?id=1714&work_id=27
 */
class GetFocusData extends BaseModule {

    protected $params = array();
    protected $errors = array();

    public function run(){
        $this->_init();
        $result = ShopInfo::getInstance()->getFocusData($this->params);

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
        );
        $this->params = $this->request()->safe();

    }
}
?>