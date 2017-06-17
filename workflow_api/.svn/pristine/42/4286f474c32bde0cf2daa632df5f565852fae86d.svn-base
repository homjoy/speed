<?php
namespace WorkFlowApi\Modules\Common;

/**
 *
 * Class BaseModule
 * @package Atom\Modules\Common
 */

use WorkFlowApi\Package\Common\Response; 

abstract class BaseModule extends \Frame\Module {

    /**
     * 是否强制检测参数，强制模式下不符合规则会直接抛出异常并自动输出错误
     * @var bool
     */
    protected $forceParamCheck = true;

    /**
     * 参数校验规则
     * @var array
     */
    protected $rules = array();
	
	protected $ip = NULL;
    protected $user;

    public function __construct($app) {
        $this->app = $app;
        $this->ip = $_SERVER['REMOTE_ADDR'];
    }
	
    /**
     * @return \Frame\Speed\Http\ParameterFilter
     */
    protected function query()
    {
        static $instance = null;
        if (is_null($instance)) {
            $instance = new \Frame\Speed\Http\ParameterFilter($this->request->GET,$this->rules,$this->forceParamCheck);
        }
        return $instance;
    }

    /**
     * @return \Frame\Speed\Http\ParameterFilter
     */
    protected function post()
    {
        static $instance = null;
        if (is_null($instance)) {
            $instance = new \Frame\Speed\Http\ParameterFilter($this->request->POST,$this->rules,$this->forceParamCheck);
        }
        return $instance;
    }

    /**
     * @return \Frame\Speed\Http\ParameterFilter
     */
    protected function request()
    {
        static $instance = null;
        if (is_null($instance)) {
            $instance = new \Frame\Speed\Http\ParameterFilter($this->request->REQUEST,$this->rules,$this->forceParamCheck);
        }
        return $instance;
    }

    protected function getClient() {   
        static $client = null;
        if (is_null($client)) {
            $client = new \Libs\Serviceclient\Client;
            \Libs\Serviceclient\RemoteHeaderCreator::setHeaders('Meilishuo', 'uid:' . $this->app->currentUser['id'] . ';ip:'.$this->ip.';v:0;master:0');
        }
        return $client;

    }

    protected function getMultiClient() {
        static $multiClient = null;
        if (is_null($multiClient)) {
            $multiClient = new \Libs\Serviceclient\MultiClient;
            \Libs\Serviceclient\RemoteHeaderCreator::setHeaders('Meilishuo', 'uid:' . $this->app->currentUser['id'] . ';ip:'.$this->ip.';v:0;master:0');
         }

        return $multiClient;
    }
    
    protected function getCurlObj() {
        static $curl_obj = NULL;
        if(is_null($curl_obj)) {
            $curl_obj = new \Libs\Sphinx\curl;
        }
        return $curl_obj;
    }
    
    protected function parseApiData( $data ) {
        $return = $data['content'];
        if (isset($return['error_code']) && $return['error_code'] == 50002) {
            return array();
        } else if( (isset($return['error_code']) && !empty($return['error_code'])) || (isset($return['code']) && !empty($return['code']) && $return['code'] != 200)) {
            
            $return = Response::gen_error($return['error_code'] ? $return['error_code'] : $return['code'], $return['error_msg']);
            $this->app->response->setBody($return);
            return FALSE;
        } else if (empty($return)) {
            $return = Response::gen_error(10000);
            $this->app->response->setBody($return);
            return FALSE;
        }
        
        return $return['data'];
    }
}
