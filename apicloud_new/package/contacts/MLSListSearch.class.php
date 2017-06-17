<?php
/**
 * 美丽说通讯录查询.
 * User: minggeng
 * Date: 16/3/28下午12:13
 */
namespace Apicloud\Package\Contacts;

use Apicloud\Package\Account\UserInfoList;
use Apicloud\Package\Account\UserAvatar;
use Apicloud\Package\Account\UserPersonalInfo;
use Apicloud\Package\Department\DepartmentInfo;
use Apicloud\Modules\Common\BaseModule;
use Apicloud\Package\Common\Response;
use Libs\Util\ArrayUtilities;
use Frame\Speed\Lib\Api;
use Apicloud\Package\Passport\Mycrypt3DES;
use Apicloud\Package\Account\MDAvtar;
use Apicloud\Package\Department\DepartmentRelation;
use Apicloud\Package\Common\BaseMemcache;
class MLSListSearch extends \Apicloud\Package\Common\BasePackage
{
    private static $instance = null;
    private $number = NULL;
    private $english = NULL;
    private $chinese = NULL;
    private $data = array();
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

    //分析输入字符
    private function _part($queryParams) {
        $queryParams = strtok($queryParams, '@');

        if(preg_match('/^[0-9]+$/', $queryParams) && strlen($queryParams) > 3) { //4个数字
            $this->number = $queryParams;
        }else if(preg_match('/^[a-zA-Z0-9]+$/', $queryParams) && strlen($queryParams) > 2) { //3个英文
            $this->english = $queryParams;
        }else if(preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $queryParams) && strlen($queryParams) > 2) { //1个汉字
            $this->chinese = $queryParams;
        }else {
            return FALSE;
        }
        return TRUE;
    }

    public function getMLSInfo($queryParams){
        //查询缓存
        $cacheKey = MEM_PREFIX.'Contacts:MLSListSearch:getMLSInfo:'.$queryParams;
        $cacheData = BaseMemcache::instance()->get($cacheKey);
        if (!empty($cacheData)) {
            return $cacheData;
        }

        $this->_part($queryParams);
        if(!is_null($this->number)) { //搜数字
            $p = array(
                'mobile'=> $this->number,
                'match' => 'like',
            );
            $data = UserPersonalInfo::getInstance()->getPersonalInfo($p);
            $data = self::_userInfo($data);
            $this->_doAppend($data);
        }else if(!is_null($this->english)) { //搜字母,字母使用精确匹配
            $p = array(
                'mail' => $this->english,
                'match' => 'like',
            );
            $data = UserInfoList::getInstance()->get($p);
            $data = self::_userInfo($data);
            $this->_doAppend($data);

            $p = array(
                'name_en' => $this->english,
                'match' => 'like',
            );
            $data = UserInfoList::getInstance()->get($p);
            $data = self::_userInfo($data);
            $this->_doAppend($data);
        }else { //搜中文,使用精确匹配
            $p = array(
                'name_cn' => $this->chinese,
                'match' => 'like',
            );
            $data = UserInfoList::getInstance()->get($p);
            $data = self::_userInfo($data);
            $this->_doAppend($data);

            // 增加部分负责人按部门搜索权限，2个字符以上才进行匹配
            if (strlen($this->chinese) > 3) {
                $userIds = self::_searchDepartment($this->chinese);
                if(!empty($userIds)){
                    $data = UserInfoList::getInstance()->get(array('user_id'=>$userIds,));
                    $data = self::_userInfo($data);
                    $this->_doAppend($data);
                }
            }
        }
        //生成缓存
        BaseMemcache::instance()->set($cacheKey, $this->data, self::$expireTime);
        return $this->data;
    }

    //从个人信息出发
    private function _userInfo($data = array()) {
        if($data === FALSE || empty($data) || isset($data['error_code'])) {
            return $data;
        }

        $uids = ArrayUtilities::my_array_column($data,'user_id');
        $p = array(
            'user_id' => $uids,
        );
        $users = UserInfoList::getInstance()->get($p);
        if(!is_array($users) || empty($users)){
            return array();
        }
        $departIds = ArrayUtilities::my_array_column($users,'depart_id');
        $deptIdStr = join(',', $departIds);

        $p = array(
            'depart_id'     => $deptIdStr,
            'display_level' => 2,
        );
        $departs = Api::atom('department/hacked_depart_info_list',$p);//部门信息
        $p = array(
            'user_id' => $uids,
        );
        $avatars = UserAvatar::getInstance()->getUserAvatar($p); //头像信息
        $idStr = implode(',', $uids);
        $mdavatar = MDAvtar::getInstance()->getMDAvtar(array('user_id'=>$idStr,)); //md头像信息
        $pinfo = UserPersonalInfo::getInstance()->getPersonalInfo(array('user_id'=>$idStr,));
        $return = array();
        foreach ($uids as $user_id) {
            if(isset($data[$user_id]) && isset($users[$user_id]) && isset($avatars[$user_id]) && isset($pinfo[$user_id])) {
                $res = array_merge($data[$user_id], $users[$user_id],$pinfo[$user_id]);
                $depart_id = $users[$user_id]['depart_id'];
                $depart_name = isset($departs[$depart_id])?$departs[$depart_id]['depart_name']:'';
                $tmp = array(
                    'depart_name' => $depart_name,
                    'name_cn' => $res['name_cn'],
                    'name_en' => $res['name_en'],
                    'mail' => $res['mail'].'@meilishuo.com',
                    'mobile' => $res['mobile'],
                );

                if(isset($avatars[$user_id])){
                    $tmp['avatar_small'] = $avatars[$user_id]['avatar_small_full'];
                    $tmp['avatar_big'] = $avatars[$user_id]['avatar_big_full'];
                }elseif(isset($mdavatar[$user_id])) {
                    $tmp['avatar_small'] = $mdavatar[$user_id]['avatar_small'];
                    $tmp['avatar_big'] = $mdavatar[$user_id]['avatar_big'];
                }
                $return[$user_id] = $tmp;
            }
        }

        return $return;
    }

    /**
     * 合并结果
     * @param array $data
     * @return boolean
     */
    private function _doAppend($data = array()) {
        if(!$data || empty($data) || !is_array($data) || isset($data['error_code'])) {
            return FALSE;
        }

        $this->data = $this->data + $data; //前提是user_id作为数组下标
    }

    private function _searchDepartment($departName = ''){
        if (empty($departName)) {
            return array();
        }
        $queryArray = array('depart_name'=> $departName,);
        $departs = DepartmentInfo::getInstance()->get($queryArray); //部门信息

        if(!is_array($departs) || empty($departs)){
            return array();
        }
        $departIds = ArrayUtilities::my_array_column($departs, 'depart_id');
        $departs = DepartmentRelation::getInstance()->get(array('depart_id' => $departIds,)); //部门信息
        $uids = ArrayUtilities::my_array_column($departs,'user_id');

        return $uids;
    }
}