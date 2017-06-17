<?php
namespace Atom\Modules\Account;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Account\UserWorkInfo;

/**
 * GetWorkInfo
 * @author hepang@meilishuo.com
 * @since 2015-08-17
 */

class GetWorkInfo extends \Atom\Modules\Common\BaseModule {

    protected $params;
    protected $errors = array();
    private $sample;

    public function run() {


        $this->_init();
        $this->sample  =  UserWorkInfo::model()->getFields();
        if($this->query()->hasError()){
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }

        //获取参数
        $queryParams = array();
        $user_id =array();
        if ($this->params['user_id'] !== '') {
            $user_id = explode(',', $this->params['user_id']);
            $queryParams['user_id'] = $user_id;
        }
        if ($this->params['mls_id'] !== '') {
            $mls_id = explode(',', $this->params['mls_id']);
            $queryParams['mls_id'] = $mls_id;
        }
        if ($this->params['contract_company_id'] !== '') {
            $contract_company_id = explode(',', $this->params['contract_company_id']);
            $queryParams['contract_company_id'] = $contract_company_id;
        }
        if ($this->params['business_company_id'] !== '') {
            $business_company_id = explode(',', $this->params['business_company_id']);
            $queryParams['business_company_id'] = $business_company_id;
        }
        if ($this->params['count'] !== '') {
            $queryParams['count'] = $this->params['count'];
        }

        if(empty($queryParams)){
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }
        if ($this->params['all'] !== '') {
            $queryParams['all'] = $this->params['all'];
        }
        if ($this->params['count'] !== '') {
            $queryParams['count'] = $this->params['count'];
        }
        //查询
        $result = UserWorkInfo::model()->getDataList($queryParams, 0, count($user_id));

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
        $this->rules = array(
            'user_id'=>array(
                'type'=>'string',
                'default'=>'',
            ),
            'mls_id'=>array(
                'type'=>'string',
                'default'=>'',
            ),
            'contract_company_id'=>array(
                'type'=>'string',
                'default'=>'',
            ),
            'business_company_id'=>array(
                'type'=>'string',
                'default'=>'',
            ),
            'count' => array(   //获取总条数
                'type' => 'integer',
            ),
            'all' => array(   //当为1时获取所有数据
                'type' => 'integer',
            ),
            'offset' => array(
                'type' => 'integer',
                'default'=>0,
            ),
            'limit' => array(
                'type' => 'integer',
                'default'=>100,
            ),
        );
        $this->params   = $this->query()->safe();
        $this->errors   = $this->query()->getErrors();
    }

}
