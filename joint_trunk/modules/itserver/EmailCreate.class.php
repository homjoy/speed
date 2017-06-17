<?php
namespace Joint\Modules\Itserver;

use Joint\Modules\Common\BaseModule;
use Joint\Package\Common\Response;
use Pixie\Exception;

/**
 * Email 数据创建
 * Class EmailCreate
 *
 * @package Joint\Modules\Itserver
 */
class EmailCreate  extends BaseModule{
    protected $errors = NULL;
    private   $params = NULL;
    const EMAIL_URL = 'http://yz.it.api01.meiliworks.com/apis/mail/core_api_v1.php';

    public function run() {

        $this->_init();
        //1. 去邮件系统查询一下，这个email 是否已经存在
        $param = array(
            'act' => 'userExist',
            'u' => $this->params['email'],
            'depart_id' =>$this->params['depart_id'],
        );

        $email_info = $this->postData($param);

        if($email_info['__STATUS__'] !='OK'){
            //检测用户存在
            $return = Response::gen_error(Response::LOCK_IS_OCCUPIED,'',$email_info['__MSG__']);
            $this->app->response->setBody($return);
            return;
        }
        //创建用户
        $create_params = array(
            'act' => 'createUser',
            'u' => $this->params['email'],
            'depart_id' =>$this->params['depart_id'],
            'truename'=>$this->params['truename']
        );
        $create_return =  $this->postData($create_params);
        if(!empty($create_return) && $create_return['__STATUS__'] =='OK' ){
            if(isset($create_return['__PASSWORD__'])&& !empty($create_return['__PASSWORD__'])){
                $return = Response::gen_success(array('passwd'=>$create_return['__PASSWORD__']));
            }else{
                $return = Response::gen_success(array('passwd'=>'Mail@gaimima'));
            }

            $this->app->response->setBody($return);
        }else{
            $return = Response::gen_error(Response::DB_NO_DATA,'',$create_return['__MSG__']);
            $this->app->response->setBody($return);
        }
    }

    /**
     * 获取参数
     * @return bool
     */
    private function _init() {

        $this->rules = array(
            'email' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'string',

            ),
            'depart_id' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'integer',
                'maxLength'=> 30
            ),
            'truename' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'string',
                'maxLength'=> 30
            ),


        );

        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
        return TRUE;
    }

    /**
     * post 数据
     * http://172.16.0.123/apis/core_api_v1.php?seckey=$seckey&act=$act&u=$u
     *
     * @param array $param
     * seckey 加密key
     * act ：方法   createUser 创建 ，deleteUser 删除  userExist 检查是否存在
     * u ： email aaa@meilishuo.com
     *
     * @return bool
     */
    protected function postData($param = array())
    {
        if (empty($param)) {
            return array();
        }
        $seckey = $this->checkKey($param['act']);

        $param['seckey'] = $seckey;
        $post_url = self::EMAIL_URL . '?'.http_build_query($param);

        $curl_obj = new \Libs\Sphinx\curl;
        $ret = $curl_obj->get($post_url);
        $body = json_decode($ret['body'],true);
        return $body;
    }

    /**
     * 获取加密数据
     * @param $act
     *
     * @return string
     */
    public  function checkKey($act){
        $ts = time();
        $sharedkey = "CoreMail2015";
        $nseckey = $sharedkey . $act . "$ts" ;
        $seckey = md5($nseckey);
        return $seckey;
    }

}
