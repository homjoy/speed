<?php
namespace Atom\Modules\Account;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Account\UserInfo;
use Atom\Package\Account\UserPersonalInfo;

/**
 * GetPersonalInfo
 * @author hongzhou@meilishuo.com
 * @since 2015-08-20
 */

class GetPersonalInfo extends \Atom\Modules\Common\BaseModule {

    protected $params;
    protected $errors = array();
    private $sample;

    public function run() {

        $this->_init();
        $this->sample  =  UserPersonalInfo::model()->getFields();
        if($this->query()->hasError()){
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }

        //获取参数
        $queryParams = array();

        if ($this->params['user_id'] !== '') {
            $user_id = explode(',', $this->params['user_id']);
	        $queryParams['user_id'] = $user_id;
        }
        if($this->params['mobile'] !== ''){
            $queryParams['mobile'] = $this->params['mobile'];
        }
        if($this->params['qq'] !== ''){
            $queryParams['qq'] = $this->params['qq'];
        }
        if($this->params['birthday'] !== ''){
            $queryParams['birthday'] = $this->params['birthday'];
        }
        if(empty($queryParams)){
           $return = Response::gen_error(10001, '', $this->query()->getErrors());
           return $this->app->response->setBody($return);
        }
        if($this->params['count'] !== ''){
            $queryParams['count'] = $this->params['count'];
        }
        if($this->params['all'] !== ''){
            $queryParams['all'] = $this->params['all'];
        }
        if($this->params['match'] !== ''){
            $queryParams['match'] = $this->params['match'];
        }
        $result = UserPersonalInfo::model()->getDataList($queryParams, $this->params['offset'], $this->params['limit']);

        if ($result === FALSE) {
            $return = Response::gen_error(50001);
        }elseif (empty($result)) {
            $return = array();
        }else{
            if( !empty($this->params['count']) ){
                $return =  Response::gen_success($result);
            }else{
                $return =  Response::gen_success(Format::outputData($result, $this->sample, TRUE));
            }
        }

        $this->app->response->setBody($return);
    }

    /**
     * 参数初始化
     */
    protected function _init()
    {   

        $this->rules  = array(
            'user_id'=>array(
                'allowEmpty'=>false,
                'type'=>'string',
            ),
//            'nation'=>array(
//                'type'=>'integer',
//                ),
            'birthday'=>array(
                'type'=>'string',
            ),
            'mobile'=>array(
                'type'=>'string',
                'maxLength' => 11,
            ),
            'qq'=>array(
                'type'=>'string',
                'maxLength' => 20,
            ),
            'match'=>array(//默认用=
                'type'=>'string',
                'default' => '=',
                'maxLength' => 8,
            ),
            'offset' => array(
                'type' => 'integer',
                'default'=>0,
            ),
            'limit' => array(
                'type' => 'integer',
                'default'=>100,
            ),
            'count' => array(   //获取总条数
                'type' => 'integer',
            ),
            'all' => array(   //当为1时获取所有数据
                'type' => 'integer',
            ),
        );
        $this->params   = $this->query()->safe();
        $this->errors   = $this->query()->getErrors();
    }

}
