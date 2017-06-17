<?php
namespace Libs\Serviceclient;
/**
 * 一个服务请求
 * @author
 */


class Request {

    private $url = '';
    private $method = 'GET';
    private $params = array();
    private $opt = array();

    public static function createRequest() {
        return new self();
    }

    public function setApi($server, $apiName) {
        $this->url = ServiceApiInfomation::getApiUrl($server, $apiName);
        $this->method = ServiceApiInfomation::getApiMethod($server, $apiName);
        $this->opt = ServiceApiInfomation::getApiOpt($server, $apiName);
    }

    public function setParam($params) {
        $this->params = (array)$params;
    }

    public function setOptions($opt) {
        if (!is_array($opt)) {
            return FALSE;
        }
        foreach ($opt as $key => $value) {
            $this->setopt($key, $value);
        }
    }

    private function setopt($type, $value) {
        switch ($type) {
            case 'timeout': 
                $this->opt['timeout'] = (int)$value;
                break;
            case 'connect_timeout':
                $this->opt['connect_timeout'] = (int)$value;
                break;
            case 'timeout_ms': 
                $this->opt['timeout_ms'] = (int)$value;
                break;
            case 'connect_timeout_ms':
                $this->opt['connect_timeout_ms'] = (int)$value;
                break;
        } 
    }

    public function __get($type) {
        if (isset($this->$type)) {
           return $this->$type; 
        }
        else {
            return array();
        }
        
    }
}

