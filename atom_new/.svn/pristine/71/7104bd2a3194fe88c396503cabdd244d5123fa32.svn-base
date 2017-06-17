<?php
namespace Atom\Modules\Account;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Core\UserLogin;
use Atom\Package\Account\UserInfo;


/**
 *
 * @author hongzhou@meilishuo.com
 * @since 2015-08-20
 */

class SearchUserInfo extends \Atom\Modules\Common\BaseModule {

    protected $params;
    protected $sample;

    public function run() {

        $this->_init();
        $this->sample  =  UserInfo::getFields();
        if(empty($this->params['name_cn']) && empty($this->params['mail']) && empty($this->params['name_en'])){
            $return = Response::gen_error(10001, '', '没有可查信息');
            return $this->app->response->setBody($return);
        }
        $queryParams = array();
        if($this->params['flag']!==''){
            $queryParams['flag'] = $this->params['flag'];
        }
        if($this->params['depart_id']!==''){
            $queryParams['depart_id'] = $this->params['depart_id'];
        }
        if($this->params['job_role_id']!==''){
            $queryParams['job_role_id'] = $this->params['job_role_id'];
        }
        if(!empty($this->params['update_time'])){
            $queryParams['update_time'] = $this->params['update_time'];
        }
        if($this->params['status']!==''){
            $queryParams['status'] = $this->params['status'];
        }
        if($this->params['count']!==''){
            $queryParams['count'] = $this->params['count'];
        }
        if($this->params['all']!==''){
            $queryParams['all'] = $this->params['all'];
        }
        if($this->params['name_cn']!==''){
            $likeParams['name_cn'] = $this->params['name_cn'];
        }
        if($this->params['mail']!==''){
            $likeParams['mail'] = $this->params['mail'];
        }
        if($this->params['name_en']!==''){
            $likeParams['name_en'] = $this->params['name_en'];
        }
        if(!empty($this->params['hire_end_time'])){
            $queryParams['hire_end_time'] = date('Y-m-d',strtotime($this->params['hire_end_time']));
        }
        if(!empty($this->params['hire_start_time'])){
            $queryParams['hire_start_time'] = date('Y-m-d',strtotime($this->params['hire_start_time']));
        }

        //查询
        $result = UserInfo::model()->getDataLike($queryParams,$likeParams,$this->params['offset'] ,$this->params['limit'] );

        if ($result === FALSE) {
            $return = Response::gen_error(50001);
        }elseif (empty($result)) {
            $return = array();
        }else{
            $return = Response::gen_success(Format::outputData($result, $this->sample, TRUE));
        }

        $this->app->response->setBody($return);
    }

    /**
     * 参数初始化
     */
    protected function _init()
    {
        $this->rules = array(
            'flag'=>array(   //标记:1实习2试用3正式4申请离职 默认查所有
                'type'=>'integer',
            ),
            'depart_id'=>array(
                'type'=>'multiId',
            ),
            'job_role_id' =>array(
                'type'=>'integer',
            ),
            'status' => array(   //在职状态:1在职2已离职3重新入职  默认查在职
                'type' => 'integer',
                'default' => array(1,3),
            ),
            'name_cn'=>array(
                'type'=>'string',
                'maxLength' => 11,
                'default' => '',
            ),
            'name_en'=>array(
                'type'=>'string',
                'maxLength' => 20,
                'default' => '',
            ),
            'mail'=>array(
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
            'hire_start_time'=> array(//入职时间查询
                'type' => 'string',
            ),
            'hire_end_time'=> array(
                'type' => 'string',
            ),
            'count' => array(   //获取总条数
                'type' => 'integer',
            ),
            'all' => array(   //当为1时获取所有数据
                'type' => 'integer',
            ),
        );
        $this->params = $this->query()->safe();
    }

}
