<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Shop\Settlement;
use Joint\Package\Common\Response;

/**
 * Class GetSettlementList
 * @package Joint\Modules\Kefu
 * @author yongzhao
 * @desc 查询近五次内结算信息
 * @wiki http://redmine.meilishuo.com/projects/doota/wiki/HOSTSsettlementsettlement_recent_list
 */
class GetSettlementList extends \Joint\Modules\Common\BaseModule {

    protected $params = array();
    protected $errors = array();

    public function run(){
        $this->_init();
        $result = Settlement::getInstance()->settlementRecentList($this->params);

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