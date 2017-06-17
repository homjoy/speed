<?php
namespace Frame\Speed\Proxy;

class RemoteHeaderCreator {

    private static $instance = NULL;

    private static $header = array('Meilishuo' => 'uid:0;ip:0.0.0.0;v:0;master:0');
    private static $info = array('ip' => '0.0.0.0', 'user_id' => 0, 'master' => 0);
    private static $uri = '';
	private static $is_mob = 0;  // 该请求是否来自mobapi或者doota

    public static function instance() {
        is_null(self::$instance) && self::$instance = new self();
        return self::$instance;
    }

    public static function getHeaders() {
        return self::$header;
    }

    public static function setHeaders($k, $v) {
        self::$header[$k] = $v;
    }

}
