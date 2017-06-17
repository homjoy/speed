<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Common\Response;
use Joint\Package\Im\ChatHistory;

/**
 * Class ImChatHistory
 * @package Joint\Modules\Kefu
 * @author yongzhao
 */
class ImChatHistory extends \Joint\Modules\Common\BaseModule {

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

        $data = ChatHistory::getInstance()->getChatHistory($this->params);

        if ($data === FALSE) {
            $data = Response::gen_error(60001);
        }

        return $this->app->response->setBody($data);
    }

    private function _init(){
        $allRules = array(
            'g_id' => array(
                'required'      => true,
                'allowEmpty'    => false,
                'type'          => 'integer',
                'default'       => 0,
            ),
            'to' => array(
                'required'      => true,
                'allowEmpty'    => false,
                'type'          =>'integer',
                'default'       =>0,
            ),
            'next_id' => array(
                'required'      => false,
                'allowEmpty'    => true,
                'type'          => 'integer',
                'default'       => 0,
            ),
            'count' => array(
                'required'      => false,
                'allowEmpty'    => true,
                'type'          => 'integer',
                'default'       => 15,
            ),
            'begin' => array(
                'required'      => true,
                'allowEmpty'    => false,
                'type'          => 'string',
                'default'       => '',
            )
        );

        $request = $this->request->GET;
        $this->rules = array_intersect_key($allRules, $request);

        $this->params  = $this->query()->safe();
        $this->errors  = $this->query()->getErrors();
    }
}
?>