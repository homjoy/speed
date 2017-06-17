<?php
namespace Apicloud\Package\Company;

/**
 * 会议室信息
 * @package Apicloud\Package\Meeting
 * @author hepang@meilishuo.com
 * @since 2015-04-16
 */

use Libs\Util\ArrayUtilities;
use Apicloud\Package\Common\BaseMemcache;

class Company extends \Apicloud\Package\Common\BasePackage{

    private static $instance = null;
    private static $expireTime = 300;

    private static $fields = array(
        'company_id'    => 0,
        'city_id'       => 0,
        'city_name'  => '',
        'company_name'  => '',
        'company_address'  => '',
        'company_addr'  => '',
        'telephone'     => '',
        'fax'           => '',
    );

    private function __construct() {}

    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function getFields()
    {
        return self::$fields;
    }

    /**
     * 查询会议室
     */
    public static function getCompanyInfo($params = array()) {

        //查询缓存
        $cacheKey = 'Company:getCompanyInfo:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($params);
        $cacheData = BaseMemcache::instance()->get($cacheKey);
        if (!empty($cacheData)) {
            return $cacheData;
        }

        //查询
        $result = self::getClient()->call('atom', 'company/company_list', $params);

        if($result['httpcode'] != 200) {
            return FALSE;
        } elseif(empty($result['content']) || $result['content']['code'] != 200) {
            return FALSE;
        }else{

            //生成缓存
            BaseMemcache::instance()->set($cacheKey, $result['content']['data'], self::$expireTime);

            return $result['content']['data'];
        }
    }

} 
