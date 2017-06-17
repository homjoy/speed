<?php

namespace Libs\Serviceclient;

/**
 * 生成snake的请求header
 * @author zx
 */
class ClientHeaderCreator {

    private static $header = array('Meilishuo' => 'uid:0;ip:0.0.0.0;v:0;master:0');
    private static $info = array('ip' => '0.0.0.0', 'user_id' => 0, 'master' => 0);
    private static $uri = '';
    private static $is_mob = 0;  // 该请求是否来自mobapi或者doota

    public static function getHeaders() {
        $v = self::is_v(self::$info['user_id'], self::$info['ip']);
        $master = !empty(self::$info['master']) ? 1 : 0;
        $is_mob = !empty(self::$is_mob) ? 1 : 0;
        self::$header = array(
                "Meilishuo" =>
                "uid:" . (int) self::$info['user_id'] .
                ";ip:" . self::$info['ip'] .
                ";v:" . $v .
                ";master:" . $master .
                ";is_mob:" . $is_mob,
                );

        if (!empty(self::$uri)) {
            self::$header['X-REF'] = self::$uri;
        }          

        return self::$header;
    }

    public static function setInfos($info) {
        self::$info = $info;

        if (isset($info['uri'])) {
            self::$uri = $info['uri'];
        }          

        if (isset($info['is_mob'])) {
            self::$is_mob = $info['is_mob'];
        }          
    }

    public static function is_v($user_id, $ip) {
        $ip = ip2long($ip);
        $v = 0;
        $white_user = array(20929830, 20719591);
        if (in_array($user_id, $white_user)) {
            $v = 1;
        }
        else {
            $v = 1;
        }
        return $v;
    }
}
