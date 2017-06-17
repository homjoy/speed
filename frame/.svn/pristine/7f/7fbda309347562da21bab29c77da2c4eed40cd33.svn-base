<?php
namespace Libs\Mark;

class FunctionLimit {
    
    private static $CacheIsLab = NULL;
    private static $CacheIsDevLab = NULL;   
    private static $CacheIsNewLab = NULL;
    private static $CacheIsPmLab = NULL;

    private static $CacheIsLab3 = NULL;
    private static $CacheIsLab6 = NULL;   
    private static $CacheIsLab1 = NULL;     

    public static function openDootaEntry($userId=0) {
        return TRUE;
        $sIp = $_SERVER['SERVER_ADDR'];  
        if (!empty($sIp) && in_array($sIp, array('172.16.0.78', '172.16.5.44','124.202.144.173','124.202.144.23'))){
            return TRUE;
        } else {
            return FALSE;
        }        
    }

    public static function openDefaultCaptchaEntry() {
        $sIp = $_SERVER['SERVER_ADDR'];  
        if (!empty($sIp) && in_array($sIp, array('172.16.0.78', '172.16.5.44','124.202.144.173','124.202.144.23'))){
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public static function openClubEntry($userId=0) {
        return TRUE;
        
        $sIp = $_SERVER['SERVER_ADDR'];  
        if (!empty($sIp) && in_array($sIp, array('172.16.0.78', '172.16.5.44','124.202.144.173','124.202.144.23'))){
            return TRUE;
        } else {
            return FALSE;
        }        
    }
    
    public static function IsLab() {
        if (!is_null(self::$CacheIsLab)) {
            return self::$CacheIsLab;
        }  
        
        $sIp = $_SERVER['SERVER_ADDR'];  
        if (!empty($sIp) && in_array($sIp, array('172.16.0.78', '172.16.5.44','124.202.144.173','124.202.144.23'))){
            self::$CacheIsLab = TRUE;
        } else {
            self::$CacheIsLab = FALSE;
        }
        
        return self::$CacheIsLab;
    }
    
    public static function IsNewLab() {
        if (!is_null(self::$CacheIsNewLab)) {
            return self::$CacheIsNewLab;
        }  
        
        $sIp = $_SERVER['SERVER_ADDR'];  
        if (!empty($sIp) && in_array($sIp, array('172.16.0.78','124.202.144.23'))){
            self::$CacheIsNewLab = TRUE;
        } else {
            self::$CacheIsNewLab = FALSE;
        }
        
        return self::$CacheIsNewLab;
    }    
    
    public static function IsDevLab() {
        if (!is_null(self::$CacheIsDevLab)) {
            return self::$CacheIsDevLab;
        }
        
        $sIp = $_SERVER['SERVER_ADDR'];  
        if (!empty($sIp) && in_array($sIp, array('172.16.5.44','124.202.144.173'))){
            self::$CacheIsDevLab = TRUE;
        } else {
            self::$CacheIsDevLab = FALSE;
        }
        
        return self::$CacheIsDevLab;
    }  
    
    public static function IsPmLab() {
        if (!is_null(self::$CacheIsPmLab)) {
            return self::$CacheIsPmLab;
        }
        
        $sIp = $_SERVER['SERVER_ADDR'];  
        if (!empty($sIp) && in_array($sIp, array('172.16.8.243', '172.16.2.15'))){
            self::$CacheIsPmLab = TRUE;
        } else {
            self::$CacheIsPmLab = FALSE;
        }
        
        return self::$CacheIsPmLab;
    }
    
    public static function IsLab1() {
        if (!is_null(self::$CacheIsLab1)) {
            return self::$CacheIsLab1;
        }
        
        $sIp = $_SERVER['SERVER_ADDR'];  
        if (!empty($sIp) && in_array($sIp, array('172.16.7.201'))){
            self::$CacheIsLab1 = TRUE;
        } else {
            self::$CacheIsLab1 = FALSE;
        }
        
        return self::$CacheIsLab1;
    }    

    public static function IsLab3() {
        if (!is_null(self::$CacheIsLab3)) {
            return self::$CacheIsLab3;
        }
        
        $sIp = $_SERVER['SERVER_ADDR'];  
        if (!empty($sIp) && in_array($sIp, array('172.16.7.202'))){
            self::$CacheIsLab3 = TRUE;
        } else {
            self::$CacheIsLab3 = FALSE;
        }
        
        return self::$CacheIsLab3;
    }   
    
    public static function IsLab6() {
        if (!is_null(self::$CacheIsLab6)) {
            return self::$CacheIsLab6;
        }
        
        $sIp = $_SERVER['SERVER_ADDR'];  
        if (!empty($sIp) && in_array($sIp, array('172.16.10.162'))){
            self::$CacheIsLab6 = TRUE;
        } else {
            self::$CacheIsLab6 = FALSE;
        }
        
        return self::$CacheIsLab6;
    }     
    
    
//     public static function openPosterBigImg($userId=0) {
//         return TRUE;
        
//         if (self::IsLab() && !empty($userId)) {
//             return TRUE;
//         } 
        
//         $currentChannel = \Snake\Libs\Base\ChannelSplitter::webSplit($_COOKIE['MEILISHUO_GLOBAL_KEY']);
        
//         if ($currentChannel%2 == 0) {
//             return TRUE;
//         }
        
//         $whiteList = array(
//             '51767397' => 1,
//             '1947660' => 1,           
//         );
        
//         if (!empty($userId) && isset($whiteList[$userId])) {
//             return TRUE;
//         }   
        
//         return FALSE;
//     } 
    
    public static function openNewPosterBigImg($userId=0) { 
        return TRUE;
    }

    public static function openDetailNewImg($userId=0) {        
        return self::openNewPosterBigImg($userId);
    } 
    
    public static function openNewShareRecommend($userId=0) {         
        if (self::IsLab()) {
            return TRUE;
        }  
        
        $global_key = $_COOKIE['MEILISHUO_GLOBAL_KEY']; 
        if (empty($global_key)) {
            return FALSE;
        }  
        
        $currentChannel = substr($global_key,-1,1);
        $currentChannel = base_convert($currentChannel, 16, 10);        
        
        if ($currentChannel%2 == 0) {
            return TRUE;
        }
        
        return FALSE;
    }
    
    public static function openMobHaitao($platform, $version) {      
        if (empty($version)) {
            return FALSE;
        }
        
        return FALSE;
        
        if (version_compare($version, '5.0.0') >= 0) {
            return TRUE;
        }  
        
        return FALSE;
    } 
    
    public static function SwitchDbtoPhpLib() {
        return self::IsLab();
    }
    
    public static function OpenBigPromoteCache($userId=0) {        
        $curTime = time();                
        $endTime = strtotime('2014-12-25 24:00:00');
        
        if ($curTime < $endTime) {
            return TRUE;
        }  
        
        if (empty($userId)) {
            return FALSE;
        }
        
        return FALSE;
    }
    
    public static function OpenBigPromoteShow() {
        $curTime = time();        
        
        $beginTime = strtotime('2014-12-22 20:00:00');
        $endTime = strtotime('2014-12-25 24:00:00');
        
        if ($curTime > $beginTime && $curTime < $endTime) {
            return TRUE;
        }  
        
        return FALSE;
    }  

    //  是否打开IM新消息提醒
    public static function OpenImNewMsgAlert(){
        return TRUE;
    } 
    
    //  是否打开推送消息提醒
    public static function OpenPushMsgAlert(){
        return TRUE;
    }     
    
    // 是否打开发现频道提醒
    public static function OpenDiscoveryAlert(){
        return TRUE;
    }     
    
    // 是否打开价格标
    public static function OpenPriceOrDiscountMarks(){
        return TRUE;
    }
    
    // 是否打开新的店铺DSR
    public static function OpenNewShopDsr(){
        return TRUE;
        /*
        if (self::IsDevLab() || self::IsPmLab() || self::IsNewLab()) {
            return TRUE;
        }
         */

        return FALSE;
    }    

    // 是否打开个性化榜单
    public static function OpenPersonalPoster() {
        return TRUE;

        return FALSE;
    }
    
    // 是否可预览
    public static function IsPreview($userid) {
        $can_userid = array(
                51767397,43265331,1383429,5318465,27069355,24798296,5091506,77737531,
                13463970,24798296,55277359,72178289,55678161,83497265,66682883,86144807,
                33155987,2013667,33634087,59884387,18708415,
                );

        $userid = intval($userid);
        if ($userid <= 0) {
            return FALSE;
        }

        if (!self::IsLab()) {
            return FALSE;
        }

        if (!in_array($userid, $can_userid)) {
            return FALSE;
        }

        return TRUE;
    } 

}
