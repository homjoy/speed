<?php
namespace Joint\Package\Common;
/**
 * Class VirusCurlTool
 * @package Joint\Package\Common
 * @author yongzhao
 */
class VirusCurlTool{

    public $curl;
    private $header = 'Meilishuo:uid:49;ip:192.168.1.1';
    private $user_agent = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 2.0.50727; Maxthon 2.0)";
    private $cookie = array();
    private $timeout = 20;
    private static $instance = null;

    private function __construct() {}

    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function setHeader($header = ''){
        if(!empty($header)){ $this->header = $header; }
        return $this;
    }

    public function setUserAgent($user_agent = ''){
        if(!empty($user_agent)){ $this->user_agent = $user_agent; }
        return $this;
    }

    public function setCookie($cookie = ''){
        if(!empty($cookie)){ $this->cookie = $cookie; }
        return $this;
    }

    private function prepare(){
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array($this->header));
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($this->curl, CURLOPT_HEADER, 0);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_USERAGENT, $this->user_agent);
        if(!empty($this->cookie)){
            curl_setopt($this->curl,CURLOPT_COOKIE, str_replace(' ', '%20', urldecode(http_build_query($this->cookie, '', '; '))));
        }
    }

    public function get($url, $params = array()){
        $this->prepare();

        $url = $url . (empty($params) ? '' : '?' . http_build_query($params));
        curl_setopt($this->curl, CURLOPT_URL, $url);

        $data = curl_exec($this->curl);

        curl_close($this->curl);
        if(isset($_REQUEST['debug'])){
            echo "<hr/>";
            var_dump($url);
            echo "<hr/>";
            var_dump($data);
            echo "<hr/>";
        }
        return $data;
    }

    public function post($url, $params = array()){
        $this->prepare();

        curl_setopt($this->curl, CURLOPT_URL, $url);
        //post请求
        if(!empty($params)){
            $params = http_build_query($params);
            curl_setopt($this->curl, CURLOPT_POST, 1);
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $params);
        }

        $data = curl_exec($this->curl);
        curl_close($this->curl);
        if(isset($_REQUEST['debug'])){
            echo "<hr/>";
            var_dump($url);
            var_dump($params);
            echo "<hr/>";
            var_dump($data);
            echo "<hr/>";
        }
        return $data;
    }
}

?>