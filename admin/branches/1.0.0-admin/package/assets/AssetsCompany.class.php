<?php
namespace Admin\Package\Assets;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Pixie\Exception;

/**
 * 供应商信息处理
 * @package Admin\Package\Assets\AssetsCompany
 * @author hongzhou@meilishuo.com
 * @since 2015-11-16
 */

class AssetsCompany extends \Admin\Package\Common\BasePackage {

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
    public  function getAssetsCompany($params = array()){
        $ret = self::getClient()->call('atom', 'executive_assets/get_assets_company_list', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }

    /**
     * @param array $params
     *
     * @return bool
     */
    public  function createAssetsCompany($params = array()){
        $ret = self::getClient()->call('atom', 'executive_assets/create_assets_company', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }

    /**
     * 更新列表
     * @param array $params
     *
     * @return bool
     */
    public  function updateAssetsCompany($params = array() ){
        $ret = self::getClient()->call('atom', 'executive_assets/update_assets_company', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
}
