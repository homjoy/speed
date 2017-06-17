<?php

namespace Joint\Package\Shop;

use Libs\Util\ArrayUtilities;
use Joint\Package\Common\BaseMemcache;
use \Joint\Package\Common\VirusCurlTool;
use Frame\Speed\Exception\ParameterException;

/**
 * Class Settlement
 * @package Joint\Package\Shop
 * @author yongzhao
 * @desc 店铺结算信息类
 */
class Settlement extends \Joint\Package\Common\BasePackage {

    const ORDER_URL = 'http://virusdoota.meilishuo.com/';
    //const ORDER_URL = 'http://virusdoota.sichongcheng.rdlab.meilishuo.com/';
    private static $instance = null;
    private static $expireTime = 259200;

    private function __construct() {}

    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param array $params
     * @return array|bool
     */
    public function settlementRecentList($params = array()){
        if(empty($params['shop_id'])){
            return array();
        }
        $params = array(
            'shop_id' => $params['shop_id']
        );
        $settlement = VirusCurlTool::getInstance()->post('settlement/settlement_recent_list', $params);

        $settlement = json_decode($settlement,true);
        //从接口时时的去读 ，如果读取不到，取混存数据
        $cacheKey = 'Joint:Settlement:settlementRecentList:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($params);
        //查询缓存
        if(empty($settlement)) {
            $settlement = BaseMemcache::instance()->get($cacheKey);
            if (!empty($settlement)) {
                $settlement = json_decode($settlement, true);
                return $settlement['data'];
            }
        }else{
            //生成缓存
            BaseMemcache::instance()->set($cacheKey, json_encode($settlement), self::$expireTime);
            return $settlement['data'];
        }

        return FALSE;
    }

    /**
     * @param array $params
     * @return mixed
     * @throws \Frame\Speed\Exception\ParameterException
     */
    public function shopBalance($params = array()){
        $type_map = array('balance', 'risk_balance', 'deposit');
        if(!in_array($params['type'], $type_map) || empty($params['shop_id'])){
            throw new ParameterException("参数有误",10004);
        }
        $balance = VirusCurlTool::getInstance()->post('mpay/mpay_query_balance', $params);

        if(empty($balance)){
            throw new ParameterException("接口获取数据失败",10004);
        }
        $balance_arr = json_decode($balance, true);
        if($balance_arr['data']['code'] == 0){
            return $balance_arr['data'];
        }else{
            throw new ParameterException($balance_arr['data']['msg'],10004);
        }
    }
}
