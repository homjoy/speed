<?php
namespace Joint\Package\Demo;

/**
 * UserInfo
 * @package Apicloud\Package\Account
 * @author hepang@meilishuo.com
 * @since 2015-08-18
 */

use Libs\Util\ArrayUtilities;
use Joint\Package\Common\BaseMemcache;

class UserInfo extends \Joint\Package\Common\BasePackage{

    private static $instance = null;
    private static $expireTime = 600;

    private static $fields = array(
            'user_id'       => 0,
            'depart_id'     => 0,  //部门id
            'job_role_id'   => 0,  //职位角色
            'name_cn'       => '', //汉语名字
            'name_en'       => '', //拼音
            'mail'          => '', //邮箱前缀
            'mail_full'     => '', //
            'hire_time'     => '', //入职时间
            'positive_time' => '', //转正时间
            'staff_id'      => '', //工号
            'gender'        => 0,  //性别0女1男
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
     * 查询
     */
    public static function getUserInfo($params = array()) {
        //查询缓存
        $cacheKey = 'Joint:UserInfo:getUserInfo:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($params);
        $cacheData = BaseMemcache::instance()->get($cacheKey);
        if (!empty($cacheData)) {
            return $cacheData;
        }

        //查询
        //只查询在职员工
        $params['status'] = array(1,3);
        $result = self::getClient()->call('atom', 'user/user_info_get', $params);

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
