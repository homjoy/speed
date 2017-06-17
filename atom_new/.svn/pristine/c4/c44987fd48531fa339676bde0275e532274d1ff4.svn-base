<?php
namespace Atom\Modules\Account;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Core\UserLogin;
use Atom\Package\Account\UserInfo;
use Atom\Package\Core\UserAccount;


/**
 * 密码过期提醒接口
 * @author haibinzhou@meilishuo.com
 * @since 2015-09-07
 */

class UserPassOutWarn extends \Atom\Modules\Common\BaseModule {

    protected $params = array();
    protected $errors = array();
    /**
     * 帐号类型
     */
    public static $TYPE_VPN = 1;
    public static $TYPE_REDMINE = 2;
    public static $TYPE_SVN = 3;
    public static $TYPE_OSYS = 4;
    public static $TYPE_MAIL_PWD = 5; //邮箱密码修改登记

    private static $max_day = 90; //最少90天修改一次密码
    private static $advance = 7; //提前7天

    public function run() {

        $this->_init();

        if(isset($this->params['user_id']) && !empty($this->params['user_id'])){
            $queryParams['user_id'] = $this->params['user_id'];
        }else if(isset($this->params['mail']) && !empty($this->params['mail'])){
            $params['mail'] = $this->params['mail'];
            $user_info = UserInfo::model()->getDataList($params);
            if(!empty($user_info)){
                $user_info = array_pop($user_info);
                $queryParams['user_id'] = $user_info['user_id'];
            }else{
                $return = Response::gen_error(10001, '用户不存在', $this->post()->getErrors());
                return $this->app->response->setBody($return);
            }
        }else{
            $return = Response::gen_error(10001, '', $this->post()->getErrors());
            return $this->app->response->setBody($return);
        }

        $queryParams['account_type'] = self::$TYPE_MAIL_PWD;

        $register_info = UserAccount::model()->getData($queryParams);  //根据邮箱类型获取用户注册信息
        $result = array();
        if(!empty($register_info)){
            $register_info = array_pop($register_info);
            $result = $this->filter_date($register_info['update_time']);
        }

        if ($result === FALSE) {
            $return = Response::gen_error(50001);
        }elseif (empty($result)) {
            $return = Response::gen_error(30001);
        }else{
            $return = Response::gen_success(Format::outputData($result));
        }

        $this->app->response->setBody($return);
    }

    //判断密码是否过期
    private function filter_date($date) {
        $length = (time() - strtotime($date)) / 86400;  //$length 单位为天
        $left = self::$max_day - $length;
        $left = ceil(floor($left));
        if($left <= 0) {
            return 1;
        }else {
            return 2;  //未过期
        }
    }

    /**
     * 参数初始化
     */
    protected function _init()
    {

        $this->rules = array(
            'user_id'=>array(
                'type'=>'integer',
            ),
            'mail' => array(
                'type' => 'string',
            ),

        );
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }

}
