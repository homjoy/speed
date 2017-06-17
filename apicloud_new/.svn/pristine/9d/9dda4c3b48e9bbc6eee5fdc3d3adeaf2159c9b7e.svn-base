<?php
namespace Apicloud\Package\Account;

/**
 * GetUserAvatar
 * @package Apicloud\Package\Account
 * @author minggeng@meilishuo.com
 * @since 2015-09-18
 */

use Libs\Util\ArrayUtilities;
use Apicloud\Package\Common\BaseMemcache;
use \Libs\Sphinx\curl;
use Frame\Speed\Exception\ParameterException;
use Apicloud\Package\Passport\Mycrypt3DES;

class MGJUserInfo extends \Apicloud\Package\Common\BasePackage{

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
    public static function getUserInfo($params = array(),$is_cache = true) {
        //查询缓存
        $cacheKey = MEM_PREFIX.'Account:MGJUserInfo:getUserInfo:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($params);
        if($is_cache){
            $cacheData = BaseMemcache::instance()->get($cacheKey);
            if (!empty($cacheData)) {
                return $cacheData;
            }
        }
        if(!isset($params['workId'])){
            return false;
        }

        //查询
        $curl_obj = new curl();
        //TODO 蘑菇街没有给链接,所以暂时先约定一个
        $result = $curl_obj->post(MOGUJIE_USERINFO_URL, $params);
        if($result['http_code'] != 200) {
            return FALSE;
        }else{
            if (empty($result) || empty($result['body'])) {
                return array();
            }
            $mgj_user_info = $result['body'];
            $decry_user_data = Mycrypt3DES::getInstance()->decrypt($mgj_user_info);
            if (empty($decry_user_data)) {
                return array();
            }
            $decry_user_array = json_decode($decry_user_data, true);
            $user_info = array();
            if (!empty($decry_user_array) && !empty($decry_user_array['code']) && $decry_user_array['code'] == 1) {
                $user_info = $decry_user_array['data'];
            }
            if(empty($user_info)){
                return array();
                //throw new ParameterException('用户信息解析失败！');
            }

            //生成缓存
            BaseMemcache::instance()->set($cacheKey, $user_info, self::$expireTime);

            return $user_info;
        }
    }

} 
