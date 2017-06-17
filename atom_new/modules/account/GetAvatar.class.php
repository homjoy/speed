<?php
namespace Atom\Modules\Account;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Account\UserAvatar;
use Libs\Util\ArrayUtilities;
/**
 * GetAvatar
 * @author hepang@meilishuo.com
 * @since 2015-08-21
 */

class GetAvatar extends \Atom\Modules\Common\BaseModule {

    protected $params;
    protected $errors = array();
    private $sample;

    public function run() {

        //defined('BEANSDB_DOMAIN_OUT') || define("BEANSDB_DOMAIN_OUT", 'http://124.202.144.25/');
        defined('BEANSDB_DOMAIN_OUT') || define("BEANSDB_DOMAIN_OUT", 'http://imgst.meilishuo.net/');
        defined("DEFAULT_PHOTO") || define("DEFAULT_PHOTO", 'http://d05.res.meilishuo.net/pic/_o/fe/90/dcc219e319f51c07c875f9498b6a_400_400.cg.jpg');

        $this->_init();

        $this->sample = UserAvatar::model()->getFields();

        if($this->query()->hasError()){
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }

        //获取参数
        $queryParams = array();
        $user_id = $this->params['user_id'];
        if ($this->params['user_id'] !== '') {
            $queryParams['user_id'] = $this->params['user_id'];
        }
        if ($this->params['status'] !== '') {
            $status = explode(',', $this->params['status']);
            $queryParams['status'] = $status;
        }

        if(empty($this->params['user_id'])){
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }

        //查询
        $result = UserAvatar::model()->getDataList($queryParams, 0, count($user_id));
        $result = ArrayUtilities::hashByKey($result,'user_id');
        if ($result === FALSE) {
            $return = Response::gen_error(50001);
        }elseif (empty($result)) {
            $return = $this->_handleNoPhotoUser($result,$user_id);
            $return = ArrayUtilities::hashByKey($return,'user_id');
        }else{
            $result = $this->_genAvatarUrl($result);
            $result = $this->_handleNoPhotoUser($result,$user_id);
            $return = Response::gen_success($result);
        }

        $this->app->response->setBody($return);
    }

    //没有头像的员工
    private function _handleNoPhotoUser($result,$user_id){
        $has_photo_user_id = ArrayUtilities::my_array_column($result,'user_id');
        if(count($user_id) > count($has_photo_user_id)){
            $no_photo_user_id = $this->_getNoPhotoUserId($user_id,$has_photo_user_id);
            foreach($no_photo_user_id as $n_user_id){
                $result[$n_user_id] = $this->_defaultPhoto($n_user_id);
            }
        }
        return $result;
    }

    //获取没有头像信息员工id
    private function _getNoPhotoUserId($user_id,$has_photo_user_id){
        $no_photo_user_id = array();

        foreach($user_id as $u_id){
            if(!in_array($u_id,$has_photo_user_id)){
                $no_photo_user_id[] = $u_id;
            }
        }
        return $no_photo_user_id;
    }

    private function _defaultPhoto($user_id){
        $result = array(
            'user_id' => $user_id,
            'avatar_src' => '',
            'avatar_big' => '',
            'avatar_middle' => '',
            'avatar_small' => '',
            'local_src' => '',
            'local_big' => '',
            'local_middle' => '',
            'local_small' => '',
            'update_time' => date('Y-m-d H:i:s'),
            'status' => '1',
            'avatar_src_full' => DEFAULT_PHOTO,
            'avatar_big_full' => DEFAULT_PHOTO,
            'avatar_middle_full' => DEFAULT_PHOTO,
            'avatar_small_full' => DEFAULT_PHOTO,
        );

        return $result;
    }

    //生成完整头像url
    private function _genAvatarUrl($avatars = array()){
        if (empty($avatars)) {
            return array();
        }
        foreach($avatars as &$avatar){
            $avatar['avatar_src_full'] = !empty($avatar['avatar_src']) ? BEANSDB_DOMAIN_OUT . $avatar['avatar_src'] : $avatar['avatar_src'];
            $avatar['avatar_big_full'] = !empty($avatar['avatar_big']) ? BEANSDB_DOMAIN_OUT . $avatar['avatar_big'] : $avatar['avatar_big'];
            $avatar['avatar_middle_full'] = !empty($avatar['avatar_middle']) ? BEANSDB_DOMAIN_OUT . $avatar['avatar_middle'] : $avatar['avatar_middle'];
            $avatar['avatar_small_full'] = !empty($avatar['avatar_small']) ? BEANSDB_DOMAIN_OUT . $avatar['avatar_small'] : $avatar['avatar_small'];
        }
        return $avatars;
    }

    /**
     * 参数初始化
     */
    protected function _init()
    {
        $this->rules = array(
            'user_id'=>array(
                'allowEmpty'=>false,
                'type'=>'multiId',
                'default'=>'',
            ),
            'status'=>array(
                'type'=>'string',
                'default'=>1,
            ),
        );
        $this->params   = $this->query()->safe();
        $this->errors   = $this->query()->getErrors();
    }

}
