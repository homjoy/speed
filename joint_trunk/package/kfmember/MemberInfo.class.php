<?php

namespace Joint\Package\Kfmember;

use Libs\Util\ArrayUtilities;
use Joint\Package\Common\BaseMemcache;
use \Joint\Package\Common\VirusCurlTool;

defined('VIP_URL') || define("VIP_URL", 'http://vip.mlservice.meilishuo.com/');
//defined('VIP_URL') || define("VIP_URL", 'http://vip.mlservice.zhaolongjiang.rdlab.meilishuo.com/');
defined('VIRUS_URL') || define("VIRUS_URL", 'http://virusdoota.meilishuo.com/');

class MemberInfo extends \Joint\Package\Common\BasePackage
{
    //获取用户等级信息
    private static $instance = null;
    private static $expireTime = 259200;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 获取美丽值和用户等级
     * @param type $params
     * @return boolean
     */
    public function getUserLevelInfo($params = array())
    {
        if (empty($params['user_id'])) {
            return FALSE;
        }
        $tmpParams = $params;
        $url = VIP_URL . 'vip/getInfo';
        $local_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
        $header = 'Meilishuo:uid:' . $params['user_id'] . ';ip:' . $local_ip;
        unset($params['user_id']);
        $userLevelInfo = VirusCurlTool::getInstance()->setHeader($header)->get($url, $params);
        $userLevelInfo = json_decode($userLevelInfo, true);
        //从接口时时的去读 ，如果读取不到，取混存数据
        $cacheKey = 'Joint:MemberInfo:getUserLevelInfo:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($tmpParams);
        //查询缓存
        if (empty($userLevelInfo)) {
            $userLevelInfo = BaseMemcache::instance()->get($cacheKey);
            if (!empty($userLevelInfo)) {
                $userLevelInfo = json_decode($userLevelInfo, true);
                return $userLevelInfo['data'];
            }
        } else {
            //生成缓存
            BaseMemcache::instance()->set($cacheKey, json_encode($userLevelInfo), self::$expireTime);
            return $userLevelInfo['data'];
        }

        return FALSE;
    }

    /**
     * 获取美美豆总数
     * @param type $params
     * @return boolean
     */
    public function getSummaryBeans($params = array())
    {
        if (empty($params['user_id'])) {
            return FALSE;
        }
        $tmpParams = $params;
        $url = VIRUS_URL . 'coin/get_summary_beans';
        $local_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
        $header = 'Meilishuo:uid:' . $params['user_id'] . ';ip:' . $local_ip . ';v:' . $params['v'] . ';master:' . $params['master'] . ';is_mob:' . $params['is_mob'];
        $summaryBeansInfo = VirusCurlTool::getInstance()->setHeader($header)->get($url, $params);
        $summaryBeansInfo = json_decode($summaryBeansInfo, true);
        //从接口时时的去读 ，如果读取不到，取混存数据
        $cacheKey = 'Joint:MemberInfo:getSummaryBeans:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($tmpParams);
        //查询缓存
        if (empty($summaryBeansInfo)) {
            $summaryBeansInfo = BaseMemcache::instance()->get($cacheKey);
            if (!empty($summaryBeansInfo)) {
                $summaryBeansInfo = json_decode($summaryBeansInfo, true);
                return $summaryBeansInfo['data'];
            }
        } else {
            //生成缓存
            BaseMemcache::instance()->set($cacheKey, json_encode($summaryBeansInfo), self::$expireTime);
            return $summaryBeansInfo['data'];
        }

        return FALSE;
    }

    /**
     * 获取美美豆总数 详情
     * @param type $params
     * @return boolean
     */
    public function getBeansDetail($params = array())
    {
        if (empty($params['user_id'])) {
            return FALSE;
        }
        $tmpParams = $params;
        $url = VIRUS_URL . 'coin/get_list';
        $local_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
        $header = 'Meilishuo:uid:' . $params['user_id'] . ';ip:' . $local_ip . ';v:' . $params['v'] . ';master:' . $params['master'] . ';is_mob:' . $params['is_mob'];
        $getParams = array(
            'page' => $params['page'],
            'count' => $params['pageSize']
        );
        $summaryBeansInfo = VirusCurlTool::getInstance()->setHeader($header)->get($url, $getParams);
        $summaryBeansInfo = json_decode($summaryBeansInfo, true);
        //从接口时时的去读 ，如果读取不到，取混存数据
        $cacheKey = 'Joint:MemberInfo:getBeansDetail:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($tmpParams);
        //查询缓存
        if (empty($summaryBeansInfo)) {
            $summaryBeansInfo = BaseMemcache::instance()->get($cacheKey);
            if (!empty($summaryBeansInfo)) {
                $summaryBeansInfo = json_decode($summaryBeansInfo, true);
                return $summaryBeansInfo['data'];
            }
        } else {
            //生成缓存
            BaseMemcache::instance()->set($cacheKey, json_encode($summaryBeansInfo), self::$expireTime);
            return $summaryBeansInfo['data'];
        }

        return FALSE;
    }


    /**
     * 获取美丽值和用户等级
     * @param type $params
     * @return boolean
     */
    public function getUserBeautyDetail($params = array())
    {
        if (empty($params['user_id'])) {
            return FALSE;
        }
        $tmpParams = $params;
        $url = VIP_URL . 'vip/GetBeautyDetail';
        $local_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
        $header = 'Meilishuo:uid:' . $params['user_id'] . ';ip:' . $local_ip;

        $getParams = array(
            'page' => $params['page'],
            'page_size' => $params['pageSize'],
            'type' => $params['type']
        );

        $userBeautyDetail = VirusCurlTool::getInstance()->setHeader($header)->get($url, $getParams);
        $userBeautyDetail = json_decode($userBeautyDetail, true);
        //从接口时时的去读 ，如果读取不到，取混存数据
        $cacheKey = 'Joint:MemberInfo:getUserBeautyDetail:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($tmpParams);
        //查询缓存
        if (empty($userBeautyDetail)) {
            $userBeautyDetail = BaseMemcache::instance()->get($cacheKey);
            if (!empty($userBeautyDetail)) {
                $userBeautyDetail = json_decode($userBeautyDetail, true);
                return $userBeautyDetail['data'];
            }
        } else {
            //生成缓存
            BaseMemcache::instance()->set($cacheKey, json_encode($userBeautyDetail), self::$expireTime);
            return $userBeautyDetail['data'];
        }

        return FALSE;
    }

    /**
     * 获取等级变更记录
     * @param array $params
     * @return bool
     */
    public function GetGradeLog($params = array())
    {
        if (empty($params['user_id'])) {
            return FALSE;
        }
        $tmpParams = $params;
        $url = VIP_URL . 'vip/GetGradeLog';
        $local_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
        $header = 'Meilishuo:uid:' . $params['user_id'] . ';ip:' . $local_ip;

        $userGradeLog = VirusCurlTool::getInstance()->setHeader($header)->get($url, array());
        $userGradeLog = json_decode($userGradeLog, true);
        //从接口时时的去读 ，如果读取不到，取混存数据
        $cacheKey = 'Joint:MemberInfo:GetGradeLog:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($tmpParams);
        //查询缓存
        if (empty($userGradeLog)) {
            $userGradeLog = BaseMemcache::instance()->get($cacheKey);
            if (!empty($userGradeLog)) {
                $userGradeLog = json_decode($userGradeLog, true);
                return $userGradeLog['data'];
            }
        } else {
            //生成缓存
            BaseMemcache::instance()->set($cacheKey, json_encode($userGradeLog), self::$expireTime);
            return isset($userGradeLog['data']) ? $userGradeLog['data'] : array();
        }

        return FALSE;
    }


}
