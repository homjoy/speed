<?php
/**
 * Created by PhpStorm.
 * User: scl
 * Date: 15-3-23
 * Time: 下午3:33
 */

namespace Libs\Util;


class EnvByRequest {

    private static $instance = NULL;
    private $santorini = '';
    private $SEASHELL = '';
    private $access_token = '';
    private $ip = '';
    private $user_id = 0;
    private $email = '';
    private $mobile = '';
    private $platform = 'PC';
    private $flash_id = '';
    private $client_id = '';
    private $device_token = '';
    private $imei = '';
    private $mac = '';
    private $udid = '';
    private $open_udid = '';
    private $called_class = '';
    private $cart_money = 0;//添加购物车金额[自定义参数]


    public static function instance($userSession, $request, $customParams = array()) {
        if (empty(self::$instance)) {
            self::$instance = new self($userSession, $request, $customParams);
        }
        return self::$instance;
    }

    public function __construct($userSession, $request, $customParams) {
        $this->request = $request;
        $this->userSession = $userSession;
        $this->customParams = $customParams;
        $this->_init();
    }

    public function getEnvParams() {
        $env = array(
          'user_id' => $this->user_id,
          'mobile' => $this->mobile,
          'email' => $this->email,
          'santorini' => $this->santorini,
          'SEASHELL' => $this->SEASHELL,
          'access_token' => $this->access_token,
          'ip' => $this->ip,
          'cart_money' => $this->cart_money,
          'platform' => $this->platform,
          'client_id' => $this->client_id,
          'device_token' => $this->device_token,
          'imei' => $this->imei,
          'mac' => $this->mac,
          'udid' => $this->udid,
          'open_udid' => $this->open_udid,
          'flash_id' => $this->flash_id,
          'called_class' => $this->called_class,
        );
        return $env;
    }

    private function _init() {
        $this->initTokenData();
        $this->initRequestData();
        $this->initCustomParamsData();
        if (isset($this->userSession['user_id']) && $this->userSession['user_id']) {
            $this->user_id = $this->userSession['user_id'];
        }
        $this->flash_id = $this->GetDeviceIdFromAccessToken();
        $this->getUserInfo();

    }

    //获取用户基本信息
    private function getUserInfo() {
        $userInfo = \Libs\Util\Users::getInstance()->getUserInfo($this->user_id);
        $this->email = $userInfo['email'];
        $mob_info = \Libs\Util\Users::getInstance()->getUserMobile($this->user_id);
        $this->mobile = $mob_info['mobile'];
    }

    private function initCustomParamsData() {
        if (isset($this->customParams['user_id']) && $this->customParams['user_id']) {
            $this->ip = $this->customParams['user_id'];
        }
        if (isset($this->customParams['ip']) && $this->customParams['ip']) {
            $this->ip = $this->customParams['ip'];
        }
        if (isset($this->customParams['cart_money']) && $this->customParams['cart_money']) {
            $this->cart_money = $this->customParams['cart_money'];
        }
        if (isset($this->customParams['device_token']) && $this->customParams['device_token']) {
            $this->device_token = $this->customParams['device_token'];
        }
        if (isset($this->customParams['open_udid']) && $this->customParams['open_udid']) {
            $this->open_udid = $this->customParams['open_udid'];
        }
        if (isset($this->customParams['imei']) && $this->customParams['imei']) {
            $this->imei = $this->customParams['imei'];
        }
        if (isset($this->customParams['mac']) && $this->customParams['mac']) {
            $this->mac = $this->customParams['mac'];
        }
        if (isset($this->customParams['called_class']) && $this->customParams['called_class']) {
            $this->called_class = $this->customParams['called_class'];
        }
        if (empty($this->called_class)) {
            $debug_backtraces = debug_backtrace();
            if ($debug_backtraces[0]['file']) {
                $this->called_class = $debug_backtraces[0]['file'];
            }
        }


    }

    private function initRequestData() {
        $cookie_key = DEFAULT_SESSION_NAME;
        if (isset($this->request->COOKIE[$cookie_key]) && $this->request->COOKIE[$cookie_key]) {
            $this->santorini = $this->request->COOKIE[$cookie_key];
        }
        if (isset($this->request->COOKIE['SEASHELL']) && $this->request->COOKIE['SEASHELL']) {
            $this->SEASHELL = $this->request->COOKIE['SEASHELL'];
        }
        if ($this->request->ip) {
            $this->ip = $this->request->ip;
        }
        if (isset($this->request->REQUEST['user_id']) && $this->request->REQUEST['user_id']) {
            $this->user_id = $this->request->REQUEST['user_id'];
        }
        if (isset($this->request->REQUEST['ip']) && $this->request->REQUEST['ip']) {
            $this->ip = $this->request->REQUEST['ip'];
        }
        if (isset($this->request->REQUEST['cart_money']) && $this->request->REQUEST['cart_money']) {
            $this->cart_money = $this->request->REQUEST['cart_money'];
        }
        if (isset($this->request->REQUEST['device_token']) && $this->request->REQUEST['device_token']) {
            $this->device_token = $this->request->REQUEST['device_token'];
        }
        if (isset($this->request->REQUEST['open_udid']) && $this->request->REQUEST['open_udid']) {
            $this->open_udid = $this->request->REQUEST['$open_udid'];
        }
        if (isset($this->request->REQUEST['imei']) && $this->request->REQUEST['imei']) {
            $this->imei = $this->request->REQUEST['imei'];
        }
        if (isset($this->request->REQUEST['mac']) && $this->request->REQUEST['mac']) {
            $this->mac = $this->request->REQUEST['mac'];
        }
        //wap cookie
        if (isset($this->request->COOKIE['app_device_token']) && $this->request->COOKIE['app_device_token']) {
            $this->device_token = $this->request->COOKIE['app_device_token'];
        }
        if (isset($this->request->COOKIE['app_open_udid']) && $this->request->COOKIE['app_open_udid']) {
            $this->open_udid = $this->request->REQUEST['app_$open_udid'];
        }
        if (isset($this->request->COOKIE['app_imei']) && $this->request->COOKIE['app_imei']) {
            $this->imei = $this->request->COOKIE['app_imei'];
        }
        if (isset($this->request->COOKIE['app_mac']) && $this->request->COOKIE['app_mac']) {
            $this->mac = $this->request->COOKIE['app_mac'];
        }



    }

    private function initTokenData() {
        //token, auth_code, client_id, user_id, expiration, type, device_token, imei, udid, mac, push_num, version
        $accessToken = \Libs\Util\AccessTokenReader::instance($this->request);
        if (!$accessToken->isValid()) {
            return FALSE;
        }
        $this->tokenData = $accessToken;
        $this->access_token = $accessToken->token;
        $this->user_id = $accessToken->user_id;
        $this->client_id = $accessToken->client_id;
        $this->device_token = $accessToken->device_token == NULL ? '' : $accessToken->device_token;
        $this->imei = $accessToken->imei;
        $this->udid = $accessToken->udid;
        $this->mac = $accessToken->mac;
        $this->platform = $this->getPlatform();
    }

    private function getDeviceIdFromAccessToken() {
        if (empty($this->tokenData)) {
            return '';
        }
        $deviceId = $this->device_token;
        if (empty($deviceId)) {
            $deviceId = $this->open_udid;
        }
        if (empty($deviceId)) {
            $deviceId = $this->udid;
        }
        if (empty($deviceId)) {
            $deviceId = $this->imei;
        }
        return $deviceId;
    }

    private function getPlatform() {
        $userAgent = $_SERVER ['HTTP_USER_AGENT'];
        if (empty ($userAgent)) {
            return 'Other';
        }
        if (FALSE !== stripos($userAgent, 'iphone')) {
            return 'iPhone';
        }
        if (FALSE !== stripos($userAgent, 'ipad')) {
            return 'iPad';
        }
        if (FALSE !== stripos($userAgent, 'ipod')) {
            return 'iPod';
        }
        if (FALSE !== stripos($userAgent, 'android')) {
            return 'Android';
        }
        return 'PC';
    }
}