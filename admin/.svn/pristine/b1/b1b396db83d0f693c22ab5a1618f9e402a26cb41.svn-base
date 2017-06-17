<?php

namespace Admin\Package\Itserver;

/**
 * 过期时间管理package
 * @package Admin\Package\UserInfo
 * @author guojiehzu@meilishuo.com
 * @since 2015-12-28
 */

class MlsAccountList extends \Admin\Package\Common\BasePackage {

    private static $instance = null;
    private function __construct() {}

    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance = new self();

        }
        return self::$instance;
    }

    /**
     * 获取过期时间
     * @param array $params
     *
     * @return bool
     */
    public  function getAccountList($params = array()){
        $ret = self::getClient()->call('atom', 'core/account_list', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }


}
