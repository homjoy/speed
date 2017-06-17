<?php
/**
 * Created by PhpStorm.
 * User: scl
 * Date: 15-3-23
 * Time: 下午4:22
 */

namespace Libs\Util;

class DBUserHelper extends \Libs\DB\DBConnManager {
    const _DATABASE_ = 'dolphin';
}

class Users {
    private static $instance;
    private $user_profile = 't_dolphin_user_profile';
    private $cols_user_profile = 'user_id, nickname, email, password, cookie, ctime, is_actived, status, realname, istested, reg_from, last_email_time, level, isPrompt, isBusiness, login_times';

    private $user_mobile_table = 't_dolphin_user_mobile';
    private $cols_user_mobile = 'user_id,mobile';

    public static function getInstance() {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {

    }

    public function getUserInfo($userId) {
        if (empty($userId)) {
            return FALSE;
        }
        $sql = "SELECT  {$this->cols_user_profile} FROM {$this->user_profile} WHERE user_id = :_user_id ";
        $userInfo = DBUserHelper::getConn()
          ->read($sql, array('_user_id' => $userId));
        return $userInfo[0];
    }

    public function getUserMobile($userId) {
        if (empty($userId)) {
            return FALSE;
        }
        $selectSql = "SELECT {$this->cols_user_mobile} FROM  {$this->user_mobile_table } WHERE user_id = :_user_id ";
        $userMobile = DBUserHelper::getConn()
          ->read($selectSql, array('_user_id' => $userId));
        return $userMobile[0];
    }

}