<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Order\UserInfo;
use Joint\Package\Common\Response;
use Joint\Modules\Common\BaseModule;

/**
 * 通过user id获取用户等级信息
 * Class GetVipInfo
 * @package Joint\Modules\Kefu
 * @date 2015-10-12
 * @author yongzhao
 * @api_wiki http://interfaces.meiliworks.com/show/interface?id=1286&work_id=30
 */
class GetVipInfo extends BaseModule {

    protected $params = array();
    protected $errors = array();

    public function run(){
        $this->_init();
        $result = UserInfo::getInstance()->getInfo($this->params);

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
            'type' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'string',
                'default'       => '',
            ),
            'logo' => array(
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