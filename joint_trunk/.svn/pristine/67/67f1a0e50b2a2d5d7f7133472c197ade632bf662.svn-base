<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Order\UserInfo;
use Joint\Package\Common\Response;
use Joint\Modules\Common\BaseModule;

/**
 * 获取用户账户余额
 * Class GetWalletBalance
 * @package Joint\Modules\Kefu
 * @author yongzhao
 * @date 2015-11-04
 * @wiki http://redmine.meilishuo.com/projects/pay-member-center/wiki/%E6%A0%B9%E6%8D%AE%E7%94%A8%E6%88%B7id%E6%88%96%E6%89%8B%E6%9C%BA%E5%8F%B7%E8%8E%B7%E5%8F%96%E9%92%B1%E5%8C%85%E4%BD%99%E9%A2%9D%E6%8E%A5%E5%8F%A3
 */
class GetWalletBalance extends BaseModule {

    protected $params = array();
    protected $errors = array();

    public function run(){
        $this->_init();
        $result = UserInfo::getInstance()->getWalletBalance($this->params);

        if ($result === FALSE) {
            $result = Response::gen_error(60001);
        }
        return $this->app->response->setBody($result);
    }

    private function _init(){
        $this->rules = array(
            'userId' => array(
                'required'      => FALSE,
                'allowEmpty'    => FALSE,
                'type'          => 'string',
                'default'       => 0,
            ),
            'mobile' => array(
                'required'      => FALSE,
                'allowEmpty'    => FALSE,
                'type'          => 'string',
                'default'       => '',
            ),
            'accessType' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'integer',
                'default'       => '',
            ),
            'sign' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'string',
                'default'       => '',
            ),
            'key' => array(
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