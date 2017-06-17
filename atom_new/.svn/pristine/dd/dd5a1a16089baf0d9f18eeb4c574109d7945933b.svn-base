<?php
namespace Atom\Modules\Account;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Avatar\MDAvtars;
use Atom\Package\Account\UserInfo;

/**
 * 生成头像
 * @author hepang@meilishuo.com
 * @since 2015-07-31
 */

class GetMdAvatar extends \Atom\Modules\Common\BaseModule {

    protected $params = array();
    protected $errors = array();

	public function run() {

        defined('UPLOAD_PATH') || define("UPLOAD_PATH", '/home/work/uploads/');
        defined('UPLOAD_DOMAIN') || define("UPLOAD_DOMAIN", 'http://uploads.speed.meilishuo.com/');

        $this->_init();

        if($this->query()->hasError()){
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }

        //默认参数
        $params = array();
        if (!empty($this->params['user_id'])) {
            $params['user_id'] = $this->params['user_id'];
        }

        if(empty($params)){
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }

        $uids = explode(',', $params['user_id']);
        $userInfo = self::_getUserInfo($uids);

        $result = array();
        if (!empty($userInfo)) {
            foreach ($userInfo as $key => $value) {
                $result[$key] = self::_genImage($value);
            }
        }

    	$this->app->response->setBody($result);
	}	

    /**
     * 参数初始化
     */
    protected function _init()
    {
        $this->rules = array(
            'user_id'=>array(
                'type'=>'string',
                'default'=>'',
            ),
        );
        $this->params   = $this->query()->safe();
        $this->errors   = $this->query()->getErrors();
    }

    //获取用户信息
    public static function _getUserInfo($uids = array()){

        if (empty($uids)) {
            return FALSE;
        }

        $queryParam = array(
            'user_id' => $uids,
            'status'  => array(1,3),
        );

        return UserInfo::model()->getDataList($queryParam, 0, count($uids));
    }

    //生成图片
    public static function _genImage($userInfo = array()){

        if (empty($userInfo)) {
            return FALSE;
        }

        $return = array(
            'avatar_small'  => UPLOAD_DOMAIN . 'mdavatar/' . $userInfo['user_id'] . '_small.png',
            'avatar_big'  => UPLOAD_DOMAIN . 'mdavatar/' . $userInfo['user_id'] . '_big.png',
        );
        $local = array(
            'avatar_small'  => UPLOAD_PATH . 'mdavatar/' . $userInfo['user_id'] . '_small.png',
            'avatar_big'  => UPLOAD_PATH . 'mdavatar/' . $userInfo['user_id'] . '_big.png',
        );

        //判断图片是否存在
        if (!file_exists($local['avatar_small']) || !file_exists($local['avatar_big'])) {

            $char = iconv_substr($userInfo['name_cn'] ,-1,1,'utf-8');

            //生成
            $Avatar = new MDAvtars($char, 512);
            $Avatar->Save($local['avatar_big'], 200);
            $Avatar->Save($local['avatar_small'], 60);
            $Avatar->Free();
        }

        return $return;
    }




}