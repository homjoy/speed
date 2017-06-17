<?php
namespace Atom\Modules\Common;

use Libs\Sphinx\curl;
/**
 *
 * Class PushIm
 * @package Atom\Modules\Common
 */
class PushIm extends \Atom\Modules\Common\BaseModule{

    protected $params;
    private static $api_host = API_HOST;
    private static $tokens = array(
        'hr' => '26043198217afee0e6872469c1e6d439',//test
        'speed' => 'c43664b9777ae403ccf7dc54558238fe',//test
        'executive' => '0bd457a2bb58b223b59aa9d39ad73e78',//test
        //'hr' => 'aee5f92fd34d97694552a4ed2c2a249a',//人力
        //'speed' => '13becdb693884d6222632766dee34365',//speed
        //'executive' => 'e225c5e8d9583107f94128d9b6ceefa1',//行政
    );
    public function run() {

       $this->_init();

        $result = $this->pushMsgByType($this->params);

        return $result;
    }


    public function pushMsgByType($params){
        $im_url = self::$api_host.'/im/publicMsg';
        if(!isset(self::$tokens[$params['type']])){
            return false;
        }
        $curl_obj = new curl;

       //$curl_obj = new \Libs\Sphinx\Curl;
        $param = array();
        $param['token']     = self::$tokens[$params['type']];
        $param['msg']       = $params['msg'];
        $param['user_ids']  = $params['user_id'];
        $param['source']  = $params['type'];
        $param['msg_type']  = '0';

        $res = $curl_obj->post($im_url,$param);

        $message = array();
        $message['param']   = $param;
        $message['res']     = $res['body'];
        $msg = json_encode($message);

        if($res['http_code'] != 200){

            //记录日志
            $this->app->log->log('speedlog/push/im_request_error.log', array(
                'msg' => $msg
            ));
        }else{

            $body = json_decode($res['body'], true);
            if ($body['code'] != 200) {

                $this->app->log->log('speedlog/push/im_callback_error.log', array(
                    'msg' => $msg
                ));

            }

            return $res;
        }

    }

    /**
     * 参数初始化
     */
    protected function _init(){
        $this->rules = array(
            'type'=>array(
                'required'=>true,
                'type'=>'string',
            ),
            'msg'=>array(
                'type'=>'string',
            ),
            'user_id'=>array(
                'required'=>true,
                'type'=>'integer',
            ),
        );

        $this->params = $this->query()->safe();


    }

}