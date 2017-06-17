<?php
namespace Atom\Modules\Account;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Account\UserWorkInfo;

/**
 * GetAllMlsId
 * @author hongzhou@meilishuo.com
 * @since 2015-01-07
 */

class GetAllMlsId extends \Atom\Modules\Common\BaseModule {

    protected $params;
    protected $errors = array();
    private $sample;

    public function run() {

        $this->_init();
        $this->sample  = array(
            'user_id'=>0,
            'mls_id' =>0,
        );//过滤
        if($this->query()->hasError()){
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }
        //获取参数
        $queryParams = array();
        if ($this->params['all'] !== '') {
            $queryParams['all'] =  $this->params['all'];
        }
        //查询
        $result = UserWorkInfo::model()->getDataList($queryParams);

        if ($result === FALSE) {
            $return = Response::gen_error(50001);
        }elseif (empty($result)) {
            $return = array();
        }else{
          $return =  Response::gen_success(Format::outputData($result, $this->sample, TRUE));
        }

        $this->app->response->setBody($return);
    }

    /**
     * 参数初始化
     */
    protected function _init()
    {
        $this->rules = array(
            'all' => array(   //获取总条数
                'type' => 'integer',
            ),
        );
        $this->params   = $this->query()->safe();
        $this->errors   = $this->query()->getErrors();
    }

}
