<?php

namespace Admin\Package\Stationery;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
/**
 *
 * @package Admin\Package\config
 * @author hongzhou@meilishuo.com
 * @since 2015-11-15
 */

class Stationery extends \Admin\Package\Common\BasePackage {

    private static $instance = null;
    private function __construct() {}

    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance = new self();

        }
        return self::$instance;
    }

    public  function getOfficeSupply($params = array()){
        $ret = self::getClient()->call('atom', 'executive_supply/get_office_supply', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }

    public  function createOfficeSupply($params = array()) {

        $ret = self::getClient()->call('atom', 'executive_supply/create_office_supply', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;

    }

    public  function updateOfficeSupply($params = array()) {

        $ret = self::getClient()->call('atom', 'executive_supply/update_office_supply', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;

    }
    public  function getOrderOfficeDetail($params = array()){
        $ret = self::getClient()->call('atom', 'executive_supply/get_office_detail', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function getOrderOfficeSupply($params = array()){
        $ret = self::getClient()->call('atom', 'executive_supply/get_order_office_supply', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
}
