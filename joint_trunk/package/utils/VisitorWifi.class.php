<?php
namespace Joint\Package\Utils;
use Joint\Package\Common\BasePackage;
/**
 * 访客wifi
 * @author hongzhou@meilishuo.com
 * @date 2015-8-11
 */
defined('VPN_WIFI_HOST') || define("VPN_WIFI_HOST", 'http://yz.it.api01.meiliworks.com/');
class  VisitorWifi  {

    private static $sharedkey = 'Pmlsit2015wifivisItor'; //共享密钥
    private static $ttl = 7200; // 有效时间(秒)

    /**
     * 创建访客使用的临时账号和密码
     * @param string $op 操作者
     * @param string $visitorName 访客姓名
     * @param string $mobile  手机
     * @param int $ttl  密码有效期(秒)
     * @param int $channel  0:员工前台开通  1:管理员后台开通
     * @return boolean
     */
    public  static function create($op, $visitorName, $mobile, $ttl, $channel) {
        $op = trim($op);
        $visitorName = trim($visitorName);
        $mobile = trim($mobile);
        $ttl = intval($ttl);
        $channel = intval($channel);

        if(empty($op) || empty($visitorName) || empty($mobile)) {
            return FALSE;
        }

        $ttl = $ttl <= 0 ? self::$ttl : $ttl;
        $channel = ($channel === 1) ? 1 : 0;

        $seckey = self::getSeckey($op);

        $param = array(
            'act'         => 'create',
            'seckey'      => $seckey,
            'op'          => $op,
            'visitorName' => $visitorName,
            'mobile'      => $mobile,
            'ttl'         => $ttl,
            'channel'     => $channel,
        );

        $url = VPN_WIFI_HOST . 'apis/wifi-visitor.php?' . http_build_query($param);
        $curl_obj = new  \Libs\Sphinx\curl;
        $ret = $curl_obj->get($url);
        if(!isset($ret['body']) || empty($ret['body'])) {
            return FALSE;
        }
        $body = $ret['body'];
        $body = json_decode($ret['body'], TRUE);
        if(json_last_error() != JSON_ERROR_NONE) {
            return FALSE;
        }

        //------ 临时记录
        //	$log_arr = $body;
        //	$log_arr['operator'] = $op;
        //$msg = json_encode($log_arr);
        // $this->app->log->log('api/package/passport/visitor_wifi.log', $msg);
        //------ 记录结束

        $return = FALSE;
        if('OK' == $body['__STATUS__']) {
            $return = array('code' => '200', 'message' => '操作成功！', 'data' => $body['__MSG__']);
        }else {
            $return = array('code' => '-1', 'message' => '操作失败！');
        }

        return $return;
    }

    /**
     *  禁用某个访客使用的临时账号
     * @param string $op 操作者
     * @param int $id 账号id
     * @return boolean|Ambigous <boolean, multitype:string >
     */
    public static function disable($op, $id) {
        if(empty($op) || empty($id)) {
            return FALSE;
        }

        $seckey = self::getSeckey($op);

        $param = array(
            'act'    => 'disable',
            'seckey' => $seckey,
            'op'     => $op,
            'id'     => $id,
        );

        $url = VPN_WIFI_HOST . 'apis/wifi-visitor.php?' . http_build_query($param);
        $curl_obj = new  \Libs\Sphinx\curl;
        $ret = $curl_obj->get($url);

        if(!isset($ret['body']) || empty($ret['body'])) {
            return FALSE;
        }
        $body = $ret['body'];
        $body = json_decode($ret['body'], TRUE);
        if(json_last_error() != JSON_ERROR_NONE) {
            return FALSE;
        }

        $return = FALSE;
        if('OK' == $body['__STATUS__']) {
            $return = array('code' => '200', 'message' => '操作成功！');
        }else {
            $return = array('code' => '-1', 'message' => '操作失败,无权限或用户不存在！');
        }

        return $return;
    }

    /**
     * 获取指定访客账号的权限
     * @param string $op 操作者
     * @param string $id 账号id
     * @return boolean|Ambigous <boolean, multitype:string >
     */
    public static function getStatus($op = '', $id = '') {
        $op = trim($op);
        $id = intval($id);
        if(empty($op) || empty($id)) {
            return FALSE;
        }

        $seckey = self::getSeckey($op);
        $get = array(
            'act'     => 'status',
            'seckey'  => $seckey,
            'id'      => $id,
        );
        $url = VPN_WIFI_HOST . 'apis/wifi-users.php?' . http_build_query($get);
        $curl_obj = new  \Libs\Sphinx\curl;
        $ret = $curl_obj->get($url);

        if(!isset($ret['body']) || empty($ret['body'])) {
            return FALSE;
        }
        $body = $ret['body'];
        $body = json_decode($ret['body'], TRUE);
        if(json_last_error() != JSON_ERROR_NONE) {
            return FALSE;
        }

        $return = FALSE;
        if('OK' == $body['__STATUS__']) {
            $return = array('code' => '200', 'message' => '操作成功！');
        }else {
            $return = array('code' => '-1', 'message' => '获取失败！');
        }

        return $return;
    }

    /**
     * 计算 seckey
     * @param string $op  邮箱前缀
     * @return boolean|string
     */
    private static function getSeckey($op = '') {
        $op = strtok($op, '@');
        $op = trim($op);

        $salt = self::$sharedkey . $op . time();
        $seckey = md5($salt);

        return $seckey;
    }


}