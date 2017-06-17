<?php

namespace Admin\Package\Worker;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Pixie\Exception;

/**
 * 消息处理
 * @package Admin\Package\Log
 * @author hongzhou@meilishuo.com
 * @since 2015-11-16
 */

class Notify extends \Admin\Package\Common\BasePackage {

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
     * 查询消息 数据
     * @param array $params
     *
     * @return bool
     */
    public function getNotify ($params = array() ){
        $ret = self::getClient()->call('atom', 'worker/get_notify_info', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
}
