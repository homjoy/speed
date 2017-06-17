<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Order\UserInfo;
use Joint\Package\Common\Response;
use Joint\Modules\Common\BaseModule;

/**
 * 获取用户的资金明细
 * Class GetWalletTrans
 * @package Joint\Modules\Kefu
 * @author yongzhao
 * @date 2015-11-04
 * @wiki http://redmine.meilishuo.com/projects/pay-member-center/wiki/%E8%8E%B7%E5%8F%96%E9%92%B1%E5%8C%85%E8%B5%84%E9%87%91%E6%98%8E%E7%BB%86%E6%8E%A5%E5%8F%A3
 */
class GetWalletTrans extends BaseModule {

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
            'queryStart_createTime' => array(
                'required'      => FALSE,
                'allowEmpty'    => FALSE,
                'type'          => 'string',
                'default'       => '',
            ),
            'queryEnd_createTime' => array(
                'required'      => FALSE,
                'allowEmpty'    => FALSE,
                'type'          => 'string',
                'default'       => '',
            ),
            'totalNum' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'integer',
                'default'       => '',
            ),
            'pageNo' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'integer',
                'default'       => '',
            ),
            'pageSize' => array(
                'required'      => FALSE,
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