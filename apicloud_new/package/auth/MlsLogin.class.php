<?php
/**
 * 美丽说登陆类.
 * User: guojiezhu
 * Date: 16/3/28下午12:13
 */
namespace Apicloud\Package\Auth;

use Frame\Speed\Exception\ParameterException;
use Apicloud\Package\Account\UserInfoList;
use Apicloud\Package\Account\UserAvatar;
use Apicloud\Package\Account\UserPersonalInfo;
use Apicloud\Package\Account\UserWorkInfo;
use Apicloud\Package\Department\DepartmentInfo;
use Apicloud\Package\Passport\Passport;
use Apicloud\Package\Company\City;

class MlsLogin extends \Apicloud\Package\Common\BasePackage
{
    private static $instance = null;
    protected $params = array();
    protected $user;

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
        $this->user = $this->_getUser();
        //验证用户名密码
        $mail = $this->params['username'] . '@' . $this->params['mail_suffix'];
        $account = Passport::checkPassport($mail, $this->params['password']);
        if ($account['code'] != 200) {
            throw new ParameterException($account['message']);
        }

        $this->account = $account;
        $this->user['password'] = md5($this->params['password']);
        $this->user['expire'] = isset($this->account['expire']) ? $this->account['expire'] : 0;
        $this->user = self::mulity($this->user); //其它信息

        return $this->user;
    }


    //检查线上用户
    private function _getUser()
    {
        if (empty($this->params['username']) || empty($this->params['password'])) {
            throw new ParameterException('请输入用户名密码！');
        }

        $user = UserInfoList::getInstance()->get(array('mail'=>$this->params['username']));
        if (empty($user)) {
            throw new ParameterException('获取用户失败');
        }
        $user = current($user);
        if (!in_array($user['status'], array(1, 3))) {
            throw new ParameterException('无权限登陆');
        }
        return $user;
    }

    private function mulity($user = array())
    {
        if (empty($user) || !isset($user['user_id'])) {
            return $user;
        }
        //头像
        $avatar = UserAvatar::getInstance()->getUserAvatar(array('user_id'=>$user['user_id']));
        if (empty($avatar) ) {
            $avatar = array('avatar_src' => '', 'avatar_big' => '', 'avatar_middle' => '', 'avatar_small' => '');
        } else {
            $avatar = current($avatar);
        }
        $user['avatar'] = $avatar;

        //个人相关
        $pinfo = UserPersonalInfo::getInstance()->getPersonalInfo(array('user_id'=> $user['user_id']));

        if (empty($pinfo)) {
            $pinfo = array();
        } else {
            $pinfo = current($pinfo);
        }
        $user['personal_info'] = $pinfo;

        //工作相关
        $winfo = UserWorkInfo::getInstance()->getWorkInfo( array('user_id'=>$user['user_id']));
        if ( empty($winfo)) {
            $winfo = array();
        } else {
            $winfo = array_pop($winfo);
        }
        $user['work_info'] = $winfo;

        //部门信息
        $dinfo = DepartmentInfo::getInstance()->get(array('depart_id'=>$user['depart_id']));
        if (empty($dinfo)) {
            $dinfo = array();
        } else {
            $dinfo = array_pop($dinfo);
        }
        $user['depart_info'] = $dinfo;

        $user = $this->formate_userinfo($user);
        return $user;
    }

    /**
     * 格式化需要返回的数据
     *
     * @param array $user
     *
     * @return array
     * @throws \Frame\Speed\Exception\ParameterException
     */
    private function formate_userinfo($user = array())
    {

        if (empty($user)) {
            throw new ParameterException('用户不存在');
        }
        //邮箱后缀

        $city_name = isset($user['work_info']) ? $user['work_info']['work_city'] : '';
        return array(
            'user_id' => isset($user['user_id']) ? $user['user_id'] : '',
            'depart_name' => isset($user['depart_info']) ? $user['depart_info']['depart_name'] : '',
            'name_cn' => isset($user['name_cn']) ? $user['name_cn'] : '',
            'name_nick' => isset($user['name_cn']) ? $user['name_cn'] : '',
            'mail' => isset($user['mail']) ? $user['mail'] .'@'.$this->params['mail_suffix']: '',
            'mobile' => isset($user['personal_info']) ? $user['personal_info']['mobile'] : '',
            'work_city' => $city_name,
            'avatar' => isset($user['avatar']) ? $user['avatar']['avatar_middle_full'] : DEFAULT_PHOTO,
            'city_id' => $this->_cityNameTocityId($city_name)
        );

    }


    /**
     * cityname 转 cityid
     * @param $name
     *
     * @return int
     */
    private function _cityNameTocityId($name){
        if(empty($name)) return 1;
        $city_info = City::getInstance()->getCityInfo(array('city_name' => $name));
        $city_info = current($city_info);
        if(empty($city_info) ||empty($city_info['city_id'] )) return 1;
        return $city_info['city_id'];

    }





}