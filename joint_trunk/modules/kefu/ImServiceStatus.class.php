<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Im\ChatHistory;
use Joint\Package\Common\Response;
use Joint\Modules\Common\BaseModule;

/**
 * 获取在线客服当前排队人数
 * Class ImServiceStatus
 * @package Joint\Modules\Kefu
 * @author yongzhao
 * @date 2015-11-02
 */
class ImServiceStatus extends BaseModule {

    protected $params = array();
    protected $errors = array();

    public function run(){
        $this->_init();
        $result = ChatHistory::getInstance()->getServiceStatus($this->params);

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
        );
        $this->params = $this->request()->safe();

    }
}
?>