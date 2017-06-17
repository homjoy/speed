<?php

namespace Joint\Package\Kfmember;

use Libs\Util\ArrayUtilities;
use Joint\Package\Common\BaseMemcache;
use \Joint\Package\Common\VirusCurlTool;
use Frame\Speed\Exception\ParameterException;

defined('VIRUS_URL') || define("VIRUS_URL", 'http://virusdoota.meilishuo.com/');

class RiskService extends \Joint\Package\Common\BasePackage {

    private static $instance = null;
    private static $expireTime = 259200;

    private function __construct() {}

    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function GetBlacklist($params){

        $url = VIRUS_URL . 'risk/Blacklist_customer_service';

        $ret = VirusCurlTool::getInstance()->post($url, $params);

        $ret = json_decode($ret, true);
        if($ret['error_code'] !== 0){
            throw new ParameterException("接口获取数据失败",10004);
        }
        return $ret['data'];
    }
}