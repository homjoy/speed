<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Common\Response;
use Joint\Package\Order\VirusOrderInfo;

/**
 * 获取仲裁详情
 * Class GetAppealDetail
 * @package Joint\Modules\Kefu
 * @author yongzhao
 * @date 2015-11-24
 */
class GetAppealDetail extends \Joint\Modules\Common\BaseModule {

    protected $params = array();
    protected $errors = array();

    public function run() {

        $this->_init();
        //获取退款的信息
        $data = VirusOrderInfo::getInstance()->getAppealDeatil($this->params);

        if ($data === FALSE) {
            $data = Response::gen_error(60001);
        }
        return $this->app->response->setBody($data);
    }

    private function _init() {
        $allRules = array(
            'refund_id' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'integer',
                'default'       => 0,
            ),
            'appeal_id' => array(
                'required'      => FALSE,
                'allowEmpty'    => FALSE,
                'type'          => 'integer',
                'default'       => 0,
            )

        );
        $request = $this->request->REQUEST;
        $this->rules = array_intersect_key($allRules, $request);
        $this->params = $this->request()->safe();

    }

}

?>