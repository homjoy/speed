<?php
namespace Frame\Speed\Proxy;

class BaseProxy {

    private $app;
    private $url = '';

    private $host = '';
    private $method = 'GET';
    private $params = array();
    public $opt = array();

    public static function createRequest() {
        return new self();
    }

    public function setServiceHost($remote) {
        $config = \Frame\ConfigFilter::instance()->getConfig('remote');
        $hosts = NULL;
        if (isset($config[$remote])) {
            $hosts = $config[$remote];
        }
        if(is_array($hosts)){
            $this->host = $hosts[array_rand($hosts)];
        }else{
            $this->host = $hosts;
        }
    }

    //url
    public function setApiUrl($api) {
        $this->url = $this->host . '/' . ltrim($api, '/');
    }

    //url
    public function setApiMethod($method) {
        $this->method = strtoupper($method);
    }

    public function setApiOpt($opt = array('timeout' => 3)) {
        $this->opt = $opt;
    }

    public function setParam($params) {
        $this->params = (array)$params;
    }

    //options
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

