<?php
namespace Atom\Modules\Account;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Core\UserLogin;
use Atom\Package\Account\UserInfo;
use Atom\Package\Account\UserAvatar;

/**
 * GetUserLogin
 * @author hepang@meilishuo.com
 * @since 2015-08-05
 */

class GetUserLogin extends \Atom\Modules\Common\BaseModule {

    protected $params;
    protected $errors = array();
    private $sample;

    public function run() {

        defined('BEANSDB_DOMAIN') || define("BEANSDB_DOMAIN", 'http://172.16.0.20/');

        $this->_init();

        $this->sample = array(
            'user_id'   => 0,
            'depart_id' => 0,
            'name_cn'   => '',
            'mail'      => '',
            'session'   => '',
            'avatar_src'    => '',
            'avatar_big'    => '',
            'avatar_middle' => '',
            'avatar_small'  => '',
        );

        if($this->query()->hasError()){
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }

        //获取参数
        $queryParams = array();
        if (!empty($this->params['session'])) {
            $queryParams['session'] = $this->params['session'];
        }

        if(empty($queryParams)){
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }

        //查询
        $result = UserLogin::model()->getDataList($queryParams, 0, 99);

        if ($result === FALSE) {
            $return = Response::gen_error(50001);
        }elseif (empty($result)) {
            $return = Response::gen_error(30001);
        }else{
            $result = current($result);
            $result = self::_getUserInfo($result);

            $return = Response::gen_success(Format::outputData($result, $this->sample));
        }

        $this->app->response->setBody($return);
    }

    /**
     * 参数初始化
     */
    protected function _init()
    {
        $this->rules = array(
            'session'=>array(
                'type'=>'string',
                'default'=>'',
            ),
        );
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }

    //获取用户信息
    public static function _getUserInfo($userToken = array()){

        if (empty($userToken['user_id'])) {
            return FALSE;
        }

        $queryParam = array(
            'user_id' => $userToken['user_id'],
            'status'  => array(1,3),
        );

        $userInfo       = UserInfo::model()->getDataList($queryParam, 0, 99);
        $userAvatar     = UserAvatar::model()->getDataList($queryParam, 0, 99);

        $userInfo = empty($userInfo) ? array() : current($userInfo);
        $userAvatar = empty($userAvatar) ? array() : current($userAvatar);
        $userAvatar = self::_genAvatarUrl($userAvatar);

        return array_merge($userToken, $userInfo, $userAvatar);
    }

    //生成完整头像url
    public static function _genAvatarUrl($avatar = array()){

        if (empty($avatar)) {
            return array();
        }

        $avatar['avatar_src'] = !empty($avatar['avatar_src']) ? BEANSDB_DOMAIN . $avatar['avatar_src'] : $avatar['avatar_src'];
        $avatar['avatar_big'] = !empty($avatar['avatar_big']) ? BEANSDB_DOMAIN . $avatar['avatar_big'] : $avatar['avatar_big'];
        $avatar['avatar_middle'] = !empty($avatar['avatar_middle']) ? BEANSDB_DOMAIN . $avatar['avatar_middle'] : $avatar['avatar_middle'];
        $avatar['avatar_small'] = !empty($avatar['avatar_small']) ? BEANSDB_DOMAIN . $avatar['avatar_small'] : $avatar['avatar_small'];

        return $avatar;
    }


}
