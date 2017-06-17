<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Order\RefundService;
use Joint\Package\Common\Response;
/**
 * Class AppealArbitrate
 * @package Joint\Modules\Kefu
 * @author yongzhao
 * @desc 仲裁处理【同意、拒绝、驳回】
 * @wiki http://redmine.meilishuo.com/projects/doota/wiki/HOSTappealappeal_arbitrate
 */
class AppealArbitrate extends \Joint\Modules\Common\BaseModule{


    protected $params = array();
    protected $errors = array();

    public function run(){

        $this->_init();

        $result = RefundService::getInstance()->appealArbitrate($this->params);

        if ($result === FALSE) {
            $result = Response::gen_error(60001);
        }
        return $this->app->response->setBody($result);
    }

    private function _init(){
        $this->rules = array(
            'refund_id' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'integer',
                'default'       => 0,
            ),
            'type' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'integer',
                'default'       => '',
            ),
            'result' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'integer',
                'default'       => '',
            ),
            'reparation_price' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'string',
                'default'       => '',
            ),
            'reason' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'string',
                'default'       => 0,
            ),
            'raparation' => array(
                'required'      => TRUE,
                'allowEmpty'    => FALSE,
                'type'          => 'integer',
                'default'       => 0,
            ),
            'op_user_id' => array(
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