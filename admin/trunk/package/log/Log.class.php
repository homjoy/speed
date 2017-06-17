<?php

namespace Admin\Package\Log;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Pixie\Exception;

/**
 * 日志信息处理
 * @package Admin\Package\Log
 * @author hongzhou@meilishuo.com
 * @since 2015-11-16
 */

class Log extends \Admin\Package\Common\BasePackage {

    private static $instance = null;
    private function __construct() {}

    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance = new self();

        }
        return self::$instance;
    }

    public  function createLogs($params = array()){
        try {
            if(!is_array($params['after_data'])){
                $params['after_data'] = base64_encode($params['after_data']);
            }
            //插入 当前用户名 当前用户user_id handle_type  handle_id  before_data  after_data
            $ret = self::getClient()->call('atom', 'log/create_admin_log', $params);

            $ret = self::parseRemoteData($ret);
            return $ret;
        }catch (Exception $e){
            return array();
        }
    }

    /**
     * 查询log 数据
     * @param array $params
     *
     * @return bool
     */
    public function getLogs ($params = array() ){
        $ret = self::getClient()->call('atom', 'log/get_admin_log', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
}
