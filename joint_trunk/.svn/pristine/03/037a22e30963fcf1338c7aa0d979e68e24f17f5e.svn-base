<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Common\Response;
use \Joint\Package\Order\OrderWeixin;

/**
 * Class GetOrderWeixin
 * @package Joint\Modules\Kefu
 * @author yongzhao
 */
class GetOrderWeixin extends \Joint\Modules\Common\BaseModule{

    protected $params = array();
    protected $errors = array();

    public function run(){

        $this->_init();

        if($this->query()->hasError()){
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }
        if(empty($this->params)){
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }

        $data = OrderWeixin::getInstance()->getOrderByTransId($this->params);

        if ($data === FALSE) {
            $data = Response::gen_error(60001);
        }

        return $this->app->response->setBody($data);

    }

    private function _init(){
        $allRules = array(
            'trans_id' => array(
                'required'      => true,
                'allowEmpty'    => false,
                'type'          => 'string',
                'default'       => '',
            ),
        );

        $request = $this->request->GET;
        $this->rules = array_intersect_key($allRules, $request);

        $this->params  = $this->query()->safe();
        $this->errors  = $this->query()->getErrors();
    }
}

?>