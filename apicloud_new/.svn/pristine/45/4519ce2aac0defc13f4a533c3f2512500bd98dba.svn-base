<?php
namespace Apicloud\Package\Company;

use Frame\Speed\Lib\Api;
use Libs\Util\ArrayUtilities;
use Apicloud\Package\Common\BaseMemcache;

/**
 * Class Office
 * @package Apicloud\Package\Company
 */
class Office extends \Apicloud\Package\Common\BasePackage{

    private static $instance = null;
    private static $expireTime = 300;

    private static $fields = array(
        'office_id'	=> 0,
        'city_id'	=> 0,
        'company_id'	=> 0,
        'office_floor'	=> 0,
        'office_name'	=> '',
        'office_position'	=> '',
        'office_address'	=> '',
        'office_telephone'	=> '',
        'office_fax'	=> '',
        'office_capacity'	=> 0,
        'office_detail'	=> '',
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
     * 查询城市
     */
    public static function getOfficeInfo($params = array()) {
        //查询缓存
        $cacheKey = 'Office:getOfficeInfo:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($params);
        //$cacheData = BaseMemcache::instance()->get($cacheKey);
        if (!empty($cacheData)) {
            return $cacheData;
        }

        try{
            $officeList = Api::atom('company/office_list', $params);
            //生成缓存
            BaseMemcache::instance()->set($cacheKey, $officeList, self::$expireTime);
        }catch (\Exception $e)
        {
            $officeList = array();
        }
        return $officeList;
    }

} 
