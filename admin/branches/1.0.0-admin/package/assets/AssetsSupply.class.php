<?php
namespace Admin\Package\Assets;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Pixie\Exception;

/**
 * 固定资产信息处理
 * @package Admin\Package\Assets\AssetsSupply
 * @author hongzhou@meilishuo.com
 * @since 2015-11-16
 */

class AssetsSupply extends \Admin\Package\Common\BasePackage {

    private static $instance = null;
    private function __construct() {}

    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance = new self();

        }
        return self::$instance;
    }

    /**
     * 获取的列表
     * @param array $params
     *
     * @return bool
     */
    public  function getAssetsSupply($params = array()){
        $ret = self::getClient()->call('atom', 'executive_assets/get_assets_supply_list', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }

    /**
     * @param array $params
     *
     * @return bool
     */
    public  function createAssetsSupply($params = array()){
        $ret = self::getClient()->call('atom', 'executive_assets/create_assets_supply', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }

    /**
     * 更新列表
     * @param array $params
     *
     * @return bool
     */
    public  function updateAssetsSupply($params = array() ){
        $ret = self::getClient()->call('atom', 'executive_assets/update_assets_supply', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
}
