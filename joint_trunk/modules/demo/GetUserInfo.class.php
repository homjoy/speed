<?php
namespace Joint\Modules\Demo;

use Libs\Util\Format;
use Joint\Package\Common\Response;
use Joint\Package\Demo\UserInfo;

/**
 * 获取用户信息
 * @author hepang@meilishuo.com
 * @since 2015-08-05
 */

class GetUserInfo extends \Joint\Modules\Common\BaseModule {

    protected $params = array();
    protected $errors = array();

	public function run() {

        $this->sample = UserInfo::getInstance()->getFields();

        $this->_init();

        if($this->query()->hasError()){
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }

        if(empty($this->params)){
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }

        $result = UserInfo::getInstance()->getUserInfo($this->params);

        if ($result === FALSE) {
            $return = Response::gen_error(60001);
        }elseif (empty($result)) {
            $return = Response::gen_error(30001);
        }else{
            $return = current($result);
            $return = Response::gen_success(Format::outputData($return, $this->sample));
        }

        $this->app->response->setBody($return);
	}

    /**
     * 参数初始化
     */
    protected function _init()
    {
        $allRules = array(
            'user_id'=>array(
                'type'=>'integer',
                'default'=>0,
            ),
            'mail'=>array(
                'type'=>'string',
                'default'=>'',
            ),
        );

        $request = $this->request->GET;
        $this->rules = array_intersect_key($allRules, $request);

        $this->params   = $this->query()->safe();
        $this->errors   = $this->query()->getErrors();
    }



}