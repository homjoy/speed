<?php
/**
 * 蘑菇街的通讯录查询.
 * User: minggeng
 * Date: 16/3/28下午12:13
 */
namespace Apicloud\Package\Contacts;

use Apicloud\Package\Passport\Mycrypt3DES;
use \Libs\Sphinx\curl;
use Apicloud\Package\Common\BaseMemcache;
class MGJListSearch extends \Apicloud\Package\Common\BasePackage
{
    private static $instance = null;
    protected $params = array();
    protected $user = array();
    private static $expireTime = 300;

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

    public function getMGJInfo($queryParams){
        //查询缓存
        $cacheKey = MEM_PREFIX.'Contacts:MGJListSearch:getMGJInfo:'.$queryParams;
        $cacheData = BaseMemcache::instance()->get($cacheKey);
        if (!empty($cacheData)) {
            return $cacheData;
        }

        $curl_obj = new curl();
        $p = array(
            'q' => $queryParams,
        );
        //TODO 蘑菇街没有给链接,所以暂时先约定一个
        $mgj_user_data = $curl_obj->post(MOGUJIE_MAILLIST_URL, $p);
        if (empty($mgj_user_data) || empty($mgj_user_data['body'])) {
            return false;
        }
        $mgj_user_info = $mgj_user_data['body'];
        $decry_user_data = Mycrypt3DES::getInstance()->decrypt($mgj_user_info);
        if (empty($decry_user_data)) {
            return false;
        }
        $decry_user_array = json_decode($decry_user_data, true);
        $user_info = array();
        if (!empty($decry_user_array) && !empty($decry_user_array['code']) && $decry_user_array['code'] == 1) {
            $user_info = $decry_user_array['data'];
            $user_info = $this->_mgjUserInfo($user_info);
        }

        //生成缓存
        BaseMemcache::instance()->set($cacheKey, $user_info, self::$expireTime);
        return $user_info;
    }

    private function _mgjUserInfo($user_info){
        if(!is_array($user_info) || empty($user_info)){
            return array();
        }
        $result = array();
        foreach($user_info as $u){
            $tmp = array(
                'depart_name' => $u['departName'],
                'name_cn' => $u['name_cn'],
                'name_en' => $u['name_nick'],
                'mail' => $u['mail'],
                'mobile' => $u['mobile'],
                'avatar_small' => isset($u['avatar'])?$u['avatar']:DEFAULT_PHOTO,
                'avatar_big' => isset($u['avatar'])?$u['avatar']:DEFAULT_PHOTO,
            );
            $result[] = $tmp;
        }
        return $result;
    }


}