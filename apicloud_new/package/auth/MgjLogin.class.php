<?php
/**
 * 蘑菇街的登陆.
 * User: guojiezhu
 * Date: 16/3/28下午12:13
 */
namespace Apicloud\Package\Auth;

use Frame\Speed\Exception\ParameterException;
use Apicloud\Package\Passport\Mycrypt3DES;

use \Libs\Sphinx\curl;
use Apicloud\Package\Company\City;
use Apicloud\Package\Account\MGJUserInfo;

class MgjLogin extends \Apicloud\Package\Common\BasePackage
{
    private static $instance = null;
    protected $params = array();
    protected $user = array();

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

    public function login($params = array())
    {
        $this->params = $params;
        $this->_getUser();
        //对账号密码加密
        $checkPass = array(
            'appKey' => MOGUJIE_APPKEY,
            'ip' => $params['ip'],
            'userName' => $this->params['username'],
            'password' => $this->params['password']
        );
        $post_params = 'ticket=' . $this->_encry(json_encode($checkPass));
        $curl_obj = new curl();

        $mgj_user_data = $curl_obj->post(MOGUJIE_URL, $post_params,false,true);

        if (empty($mgj_user_data) || empty($mgj_user_data['body']) ) {
            throw new ParameterException('获取用户失败',10003);
        }
        $mgj_user_info = $mgj_user_data['body'];
        $decry_user_data = $this->_decry($mgj_user_info);
        if (empty($decry_user_data)) {
            throw new ParameterException('获取用户失败',10004);
        }
        $decry_user_array = json_decode($decry_user_data, true);

        $user_info = array();
        if (!empty($decry_user_array) && !empty($decry_user_array['code']) && $decry_user_array['code'] == 200 && $decry_user_array['success']) {
            $user_info = $decry_user_array['data'];
            $worker_id = isset($user_info['parameter']['workId']) ? $user_info['parameter']['workId'] : 0;
            if (empty($worker_id)) {
                throw new ParameterException('获取用户失败',10001);
            }
            $other_info = $this->_getUserOtherInfoByWorkerId($worker_id);
            $user_info['other_info'] = $other_info;
            $user_info = $this->_formate_userinfo($user_info);
        } else {
            throw new ParameterException($decry_user_array['msg']);
        }
        if (empty($user_info)) {
            throw new ParameterException('获取用户失败',10002);
        }
        $this->user = $user_info;
        $this->user['password'] = md5($this->params['password']);
        $this->user['expire'] = 0;
        return $this->user;
    }


    //检查线上用户
    private function _getUser()
    {
        if (empty($this->params['username']) || empty($this->params['password'])) {
            throw new ParameterException('请输入用户名密码！');
        }

    }

    /**
     * 获取用户的相关数据
     *
     * @param $workerId
     *
     * @return array
     */
    private function _getUserOtherInfoByWorkerId($workId)
    {
        if (empty($workId)) return array();
        //$workId = '00001';
        $search_array = array(
            'workId' => $workId
        );
        $user_other_info = MGJUserInfo::getInstance()->getUserInfo($search_array,false);
        return $user_other_info;
        //return array();
    }

    /**
     * 格式化需要返回的数据
     *
     * @param array $user
     *
     * @return array
     * @throws \Frame\Speed\Exception\ParameterException
     */
    private function _formate_userinfo($user = array())
    {
        if (empty($user)) {
            throw new ParameterException('用户不存在');
        }
        $city_name = isset($user['other_info']['work_city']) ? $user['other_info']['work_city'] : '';
        return array(
            'user_id' => isset($user['parameter']['workId']) ? $user['parameter']['workId'] : '',
            'depart_name' => isset($user['other_info']['departName']) ? $user['other_info']['departName'] : '',
            'name_cn' => isset($user['userName']) ? $user['userName'] : '',
            'name_nick' => isset($user['parameter']['nickName']) ? $user['parameter']['nickName'] : '',
            'mail' => isset($user['parameter']['domain']) ? $user['parameter']['domain'] . '@' . $this->params['mail_suffix'] : '',
            'mobile' => isset($user['other_info']['mobile']) ? $user['other_info']['mobile'] : '',
            'work_city' => $city_name,
            'avatar' => isset($user['parameter']['avatar']) ? $user['parameter']['avatar'] : DEFAULT_PHOTO,
            'city_id' => $this->_cityNameTocityId($city_name),
        );

    }

    /**
     * 加密
     *
     * @param $str
     *
     * @return mixed
     */
    private function _encry($str)
    {
        return urlencode(Mycrypt3DES::getInstance()->encrypt($str));
    }

    /**
     * 解密
     *
     * @param $str
     *
     * @return mixed
     */
    private function _decry($str)
    {
        $str = urldecode($str);
        return Mycrypt3DES::getInstance()->decrypt($str);
    }


    /**
     * cityname 转 cityid
     *
     * @param $name
     *
     * @return int
     */
    private function _cityNameTocityId($name)
    {
        if (empty($name)) return 1;
        $city_info = City::getInstance()->getCityInfo(array('city_name' => $name));
        if (empty($city_info)) return 1;
        $city_info = current($city_info);
        if (empty($city_info) || empty($city_info['city_id'])) return 1;
        return $city_info['city_id'];

    }
}