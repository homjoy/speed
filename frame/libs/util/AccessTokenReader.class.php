<?php
/**
 * Created by PhpStorm.
 * User: scl
 * Date: 15-3-23
 * Time: 下午4:01
 */

namespace Libs\Util;

class DBSwanHelper extends \Libs\DB\DBConnManager {
    const _DATABASE_ = 'swan';
}

class AccessTokenReader {
    /**
     * 操作的数据库表名
     */
    private static $access_token_table = 't_swan_oauth_access_token';
    private static $col = 'token, auth_code, client_id, user_id, expiration, type, device_token, imei, udid, mac, push_num, version';

    private $token = NULL;
    private $tokenData = array();
    private static $_instance = NULL;

    private function __construct($token) {
        $this->token = $token;
        $this->initTokenData();
    }

    private function  __clone() {
    }

    public static function instance($request) {
        if (is_null(self::$_instance) || !isset(self::$_instance)) {
            isset($request->REQUEST['app_access_token']) && $access_token = $request->REQUEST['app_access_token'];
            if (empty($access_token)) {
                isset($request->REQUEST['access_token']) && $access_token = $request->REQUEST['access_token'];
            }
            empty($access_token) && $access_token = isset($request->COOKIE['app_access_token']) ? $request->COOKIE['app_access_token'] : '';

            self::$_instance = new self($access_token);
        }
        return self::$_instance;
    }

    private function initTokenData() {
        if (empty($this->token)) {
            return;
        }
        $tokenData = $this->getTokenDataFromDB();
        if (!empty($tokenData)) {
            $this->tokenData = $tokenData;
            return;
        }
    }


    public function isValid() {
        if (!empty($this->tokenData) && !empty($this->tokenData['token'])) {
            return TRUE;
        }
        return FALSE;
    }

    public function __get($field) {
        if (isset($this->tokenData[$field])) {
            return $this->tokenData[$field];
        }
        return NULL;
    }

    private function getTokenDataFromDB() {
        $sql = "SELECT " . self::$col . " FROM " . self::$access_token_table . " WHERE token = :token AND (expiration = 0 || expiration >= :_timestamp)";
        $params = array(
          'token' => $this->token,
          '_timestamp' => $_SERVER['REQUEST_TIME'],
        );
        $result = DBSwanHelper::getConn()->read($sql, $params);
        !empty($result) && $result = $result[0];
        return $result;
    }
}