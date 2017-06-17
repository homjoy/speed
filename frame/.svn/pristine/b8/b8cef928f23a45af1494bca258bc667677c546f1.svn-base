<?php

namespace Libs\Serviceclient;

/**
 * 生成请求header
 * @author zx
 */
class RemoteHeaderCreator {

    private static $header = array('Meilishuo' => 'uid:0;ip:0.0.0.0;v:0;master:0');
    private static $info = array('ip' => '0.0.0.0', 'user_id' => 0, 'master' => 0);
    private static $uri = '';
	private static $is_mob = 0;  // 该请求是否来自mobapi或者doota

    public static function getHeaders() {
        return self::$header;
    }

    public static function setHeaders($k, $v) {
        self::$header[$k] = $v;
    }

}
