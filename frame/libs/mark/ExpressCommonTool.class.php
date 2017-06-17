<?php
namespace Libs\Mark;

use \Libs\Cache\Memcache;
use \Libs\Mark\FunctionLimit;

class DBExpressCommonToolHelper extends \Libs\DB\DBConnManager {
    const _DATABASE_ = 'dolphin';
}

class ExpressCommonTool {
    private static $express_common_tool_table = 't_dolphin_content_data';

    public static function getExpressInfo($params, $cols = "*", $fromMaster = FALSE, $hashKey = NULL) {
        if (empty($params)) {
            return FALSE;
        }

        $selectSql = "SELECT $cols FROM " . self::$express_common_tool_table . " WHERE 1 = 1";
        $sqlData = array();
        foreach ($params as $paramKey => $paramValue) {
            if (is_array($paramValue)) {
                $selectSql .= " AND `$paramKey` IN ('" . implode("','", $paramValue) . "')";
            } else {
                $selectSql .= " AND `$paramKey` = :$paramKey";
                $sqlData[$paramKey] = $paramValue;
            }
        }
        $expressInfo = DBExpressCommonToolHelper::getConn()->read($selectSql, $sqlData, $fromMaster, $hashKey);
        return $expressInfo;
    }

    public static function getExpressInfoByPassword($password) {
        if (empty($password)) {
            return array();
        }
               
        $cacheTime = 300;
        if (FunctionLimit::OpenBigPromoteCache()) {
            $cacheTime = 1200;
        }          
        
        $key = 'Mob:ExpressCommonTool:Pass:' . date('H') . $password;
        $cacheHelper = Memcache::instance();        
        $data = $cacheHelper->get($key);
        $isLab = FunctionLimit::IsLab() || FunctionLimit::IsPmLab();
        if (!empty($data) && is_array($data) && $isLab == false) {
            return $data;
        }                 
        
        $params = array('password' => $password);
        $dbData = self::getExpressInfo($params);  
        
        if (!empty($dbData)) {         
            $cacheHelper->set($key, $dbData, $cacheTime);
        }
        
        return $dbData;
    }
    
    public static function getExpressInfoByCid($contentId) {
        $contentId = intval($contentId);        
        if (empty($contentId)) {
            return FALSE;
        }
        
        $cacheTime = 300;
        if (FunctionLimit::OpenBigPromoteCache()) {
            $cacheTime = 1200;
        }          
        
        $h = date('H');
        $key = "Mob:ExpressCommonTool:Cid:{$h}:" . $contentId;
        $cacheHelper = Memcache::instance();
        $data = $cacheHelper->get($key);
        $isLab = FunctionLimit::IsLab() || FunctionLimit::IsPmLab();
        if (!empty($data) && is_array($data) && $isLab == false) {
            return $data;
        }                 
        
        
        $params = array('content_id' => $contentId);        
        $dbData = self::getExpressInfo($params);
        
        if (!empty($dbData[0]['data_json'])) {  
            $cacheHelper->set($key, $dbData, $cacheTime);
        }
        
        return $dbData;        
    }    
}
