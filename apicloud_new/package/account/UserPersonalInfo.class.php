<?php
namespace Apicloud\Package\Account;

/**
 * Account
 * @package Apicloud\Package\Account
 * @author gengming@meilishuo.com
 * @since 2015-10-10
 */

use Libs\Util\ArrayUtilities;
use Apicloud\Package\Common\BaseMemcache;

class UserPersonalInfo extends \Apicloud\Package\Common\BasePackage{

    private static $instance = null;
    private static $expireTime = 600;

    private function __construct() {}

    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 查询
     */
    public static function getPersonalInfo($params = array(),$is_cache = true) {
        //查询缓存
        $cacheKey = MEM_PREFIX.'Account:getPersonalInfo:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($params);
        if($is_cache) {
            $cacheData = BaseMemcache::instance()->get($cacheKey);
            if (!empty($cacheData)) {
                return $cacheData;
            }
        }

        //查询
        $result = self::getClient()->call('atom', 'account/get_personal_info', $params);

        if($result['httpcode'] != 200) {
            return FALSE;
        } elseif(empty($result['content']) || $result['content']['error_code'] != 0) {
            return FALSE;
        }else{

            $data = $result['content']['data'];

            //生成缓存
            BaseMemcache::instance()->set($cacheKey, $data, self::$expireTime);

            return $data;
        }
    }

} 
