<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Common\Response;
use Joint\Package\Kfmember\RiskService;

/**
 * 根据user_id获取中风控的情况
 * Class BlacklistCustomerService
 * @package Joint\Modules\Kefu
 * @author yongzhao
 * @date 2015-11-23
 */
class BlacklistCustomerService extends \Joint\Modules\Common\BaseModule {

    protected $params = array();
    protected $errors = array();

    public function run(){
        $this->_init();


        $data = RiskService::getInstance()->GetBlacklist($this->params);

        if ($data === FALSE) {
            $data = Response::gen_error(60001);
        }

        return $this->app->response->setBody($data);
    }

    private function _init(){
        $allRules = array(
            'user_id' => array(
                'required'      => true,
                'allowEmpty'    => false,
                'type'          => 'string',
                'default'       => '',
            ),
        );
        $request = $this->request->REQUEST;
        $this->rules = array_intersect_key($allRules, $request);

        $this->params  = $this->query()->safe();

    }
}


?>