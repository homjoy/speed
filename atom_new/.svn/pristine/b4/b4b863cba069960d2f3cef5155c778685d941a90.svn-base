<?php
namespace Atom\Modules\Account;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Account\UserPersonalInfo;

/**
 * SearchPersonalInfo
 * @author hongzhou@meilishuo.com
 * @since 2015-08-19
 */

class SearchPersonalInfo extends \Atom\Modules\Common\BaseModule {

    protected $params;
    protected $sample;

    public function run() {

        $this->_init();
        $this->sample  =  UserPersonalInfo::getFields();
        if (empty($this->params['mobile']) && empty($this->params['qq'])) {
            $return = Response::gen_error(10001, '', '啥消息也不给,主人我真的很难做的');
            return $this->app->response->setBody($return);
        }
        $queryParams =  array();
        if(!empty($this->params['count'])){
            $queryParams['count'] = $this->params['count'];
        }
        if(!empty($this->params['all'])){
            $queryParams['all'] = $this->params['all'];
        }
        //like字段
        $likeParams['mobile']= $this->params['mobile'];
        $likeParams['qq']= $this->params['qq'];

        //查询
        $result = UserPersonalInfo::model()->getDataLike($queryParams,$likeParams,$this->params['offset'], $this->params['limit']);
        
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
            'mobile'=>array(
                'type'=>'string',
                'maxLength' => 11,
                'default' => '',
            ),
            'qq'=>array(
                'type'=>'string',
                'maxLength' => 20,
                'default' => '',
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
    }

}
