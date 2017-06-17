<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Common\Response;
use Joint\Package\Order\VirusOrderInfo;

/**
 * 获取仲裁信息
 * Class GetHigoOrderInfo
 * @package Joint\Modules\Kefu
 * @author guojiezhu
 */
class GetAppealList extends \Joint\Modules\Common\BaseModule {

    protected $params = array();
    protected $errors = array();

    public function run() {

        $this->_init();
        //获取退款的信息
        $data = VirusOrderInfo::getInstance()->getAppealList($this->params);
        
        if ($data === FALSE) {
            $data = Response::gen_error(60001);
        }
        //返回详细信息
//        foreach($data as $key => $val){
//            $params = array(
//                'appeal_id' => $val['appeal_id'],
//				'refund_id' => $val['refund_id'],
//            );
//            $data[$key]['appealInfo'] = VirusOrderInfo::getInstance()->getAppealDeatil($params);
//        }
        return $this->app->response->setBody($data['data']);
    }

    private function _init() {
        $allRules = array(
            'order_id' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'string',
                'default'       => 0,
            )
           
        );
        $request = $this->request->REQUEST;
        $this->rules = array_intersect_key($allRules, $request);
        $this->params = $this->request()->safe();

    }

}

?>