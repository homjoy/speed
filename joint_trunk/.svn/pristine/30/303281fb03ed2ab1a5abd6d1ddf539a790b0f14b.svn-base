<?php
namespace Joint\Package\Im;

use Libs\Util\ArrayUtilities;
use Joint\Package\Common\BaseMemcache;
use Joint\Package\Common\VirusCurlTool;
use Frame\Speed\Exception\ParameterException;

/**
 * Class ChatHistory
 * @package Joint\Package\im
 * @author yongzhao
 */
defined('IM_SNAKE_URL') || define("IM_SNAKE_URL", 'http://imsnake.meilishuo.com/');
defined('IM_VIRUS_URL') || define("IM_VIRUS_URL", 'http://virusim.meilishuo.com/');

class ChatHistory extends \Joint\Package\Common\BasePackage{

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
    public static function getChatHistory($params = array()) {


        $history = VirusCurlTool::getInstance()->post(IM_SNAKE_URL . "im/open_history_workorder", $params);

        $cacheKey = 'Joint:ChatHistory:getChatHistory:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($params);
        if(!empty($history)){
            //生成缓存
            BaseMemcache::instance()->set($cacheKey, $history, self::$expireTime);
        }else{
            //查询缓存
            $history = BaseMemcache::instance()->get($cacheKey);
        }
        $historyArr = json_decode($history, true);

        $returnData = !empty($historyArr['data']) ? $historyArr['data'] : FALSE;
        return $returnData;
    }

    /**
     * 获取当前排队人数
     * @param array $params
     * @return mixed
     * @throws \Frame\Speed\Exception\ParameterException
     */
    public static function getServiceStatus($params = array()){

        $url = IM_VIRUS_URL . 'im/service_status';

        $ret = VirusCurlTool::getInstance()->GET($url, $params);

        $ret = json_decode($ret, true);
        if($ret['error_code'] !== 0){
            throw new ParameterException("获取排队人数失败",10004);
        }
        return $ret['data'];
    }

}
