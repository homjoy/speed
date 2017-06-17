<?php
namespace Apicloud\Package\Notify;

/**
 * UserInfoMonitor
 * @package Apicloud\Package\Notice
 * @author hepang@meilishuo.com
 * @since 2015-07-19
 */

use Libs\Util\ArrayUtilities;
use Apicloud\Package\Common\BaseMemcache;

class UserInfoMonitor extends \Apicloud\Package\Common\BasePackage{

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
     * 监控链接
     * @var array
     */
    public static $monitor = array(
        'mls_id' => array(
            'url' => '/user/',
            'message' => '请填写美丽说ID',
        ),
        'user_avatar' => array(
            'url' => '/user/avatar/',
            'message' => '请上传头像',
        ),
/*        
        'password' => array(
            'url' => '/user/password',
            'message' => '请修改密码',
        ),
*/
    );

    /**
     * 检测用户
     */
    public static function getMonitor($user_id = 0)
    {
        if (empty($user_id)) {
            return FALSE;
        }

        foreach (self::$monitor as $key => $value) {
            //如果为TRUE，则提醒跳转
            $stat = self::checkStatus($key, $user_id);
            if ($stat) {
                return $value;
            }
        }

        return NULL;
    }

    /**
     * 检测状态
     */
    public static function checkStatus($field = '', $user_id = 0)
    {
        switch ($field) {
            //如果mls_id=0有数据，则为真，跳转
            case 'mls_id':
                $res = self::checkMlsId($user_id);
                return !empty($res) ? TRUE : FALSE;
                break;
            //如果user_avatar为空，则为真，跳转
            case 'user_avatar':
                $res = self::checkAvatar($user_id);
                return empty($res) ? TRUE : FALSE;
                break;
            case 'password':
                return self::checkPassword($user_id);
                break;

            default:
                return FALSE;
                break;
        }
    }

    /**
     * 检测美丽说id状态
     */
    public static function checkMlsId($user_id = 0) {
        //查询缓存
        $cacheKey = MEM_PREFIX.'UserInfoMonitor:checkMlsId:'.$user_id;
        $cacheData = BaseMemcache::instance()->get($cacheKey);
        if (!empty($cacheData)) {
            //return $cacheData;
        }

        //查询
        $result = self::getClient()->call('atom', 'account/get_work_info', array('user_id'=>$user_id, 'mls_id'=>0,));

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

    /**
     * 刷新美丽说id状态
     */
    public static function refreshMlsId($user_id = 0) {
        $cacheKey = MEM_PREFIX.'UserInfoMonitor:checkMlsId:'.$user_id;
        BaseMemcache::instance()->delete($cacheKey);
    }

    /**
     * 查询是否上传头像
     */
    public static function checkAvatar($user_id = 0) {
        //查询缓存
        $cacheKey = MEM_PREFIX.'UserInfoMonitor:checkAvatar:'.$user_id;
        $cacheData = BaseMemcache::instance()->get($cacheKey);
        if (!empty($cacheData)) {
            //return $cacheData;
        }

        //查询
        $result = self::getClient()->call('atom', 'account/get_avatar', array('user_id'=>$user_id, 'status'=>1,));

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

    /**
     * 刷新美丽说id状态
     */
    public static function refreshAvatar($user_id = 0) {
        $cacheKey = MEM_PREFIX.'UserInfoMonitor:checkAvatar:'.$user_id;
        BaseMemcache::instance()->delete($cacheKey);
    }

} 
