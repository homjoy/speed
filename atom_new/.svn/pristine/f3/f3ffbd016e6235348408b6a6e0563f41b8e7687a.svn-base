<?php
namespace Atom\Modules\Account;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Core\UserLogin;
use Atom\Package\Account\UserInfo;


/**
 *
 * @author haibinzhou@meilishuo.com ／hongzhou@meilishuo.com
 * @since 2015-08-19
 */

class GetUserInfoHigo extends \Atom\Modules\Common\BaseModule {

    protected $params;
    protected $errors = array();
    private  $sample;

    public function run() {

        $this->_init();
        $this->sample  =  UserInfo::model()->getFields();
        $queryParams =array();
        if($this->params['hire_end_time']!=''){
            $queryParams['hire_end_time'] = date('Y-m-d',strtotime($this->params['hire_end_time']));
        }
        if($this->params['hire_start_time']!=''){
            $queryParams['hire_start_time'] = date('Y-m-d',strtotime($this->params['hire_start_time']));
        }
        if($this->params['user_id']!=''){
            $queryParams['user_id'] = $this->params['user_id'];
        }
        if($this->params['staff_id']!=''){
            $queryParams['staff_id'] = $this->params['staff_id'];
        }
        if($this->params['flag']!=''){
            $queryParams['flag'] = $this->params['flag'];
        }
        if($this->params['status']!=''){
            $queryParams['status'] = $this->params['status'];
        }
        if($this->params['depart_id']!=''){
            $queryParams['depart_id'] = $this->params['depart_id'];
        }
        if($this->params['job_role_id']!=''){
            $queryParams['job_role_id'] = $this->params['job_role_id'];
        }
        if($this->params['update_time']!=''){
            $queryParams['update_time'] = $this->params['update_time'];
        }
        if($this->params['name_cn']!=''){
            $queryParams['name_cn'] = $this->params['name_cn'];
        }
        if($this->params['name_en']!=''){
            $queryParams['name_en'] = $this->params['name_en'];
        }
        if($this->params['mail']!=''){
            $queryParams['mail'] = $this->params['mail'];
        }
        if($this->params['end_time']!=''){
            $queryParams['end_time'] = $this->params['end_time'];
        }

        //校验是否有限定参数
        if(empty($queryParams)){
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }
        if($this->params['count']!=''){
            $queryParams['count'] = $this->params['count'];
        }
        if($this->params['match']!=''){
            $queryParams['match'] = $this->params['match'];
        }
        if($this->params['all']!=''){
            $queryParams['all'] = $this->params['all'];
        }
        $result = UserInfo::model()->getDataList($queryParams,$this->params['offset'], $this->params['limit']);

        if ($result === FALSE) {
            $return = Response::gen_error(50001);
        }else if(empty($result)){
            $return = array(
                'user_id' => -1,
            );
        }else{
            if(!empty($this->params['count']) ){
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
                'type'=>'multiId',
                'default' =>'',
            ),
            'flag'=>array(   //标记:1实习2试用3正式4申请离职 默认查所有
                'type'=>'integer',
            ),
            'depart_id'=>array(
                'type'=>'multiId',
                'default' =>'',
            ),
            'job_role_id' =>array(
                'type'=>'integer',
            ),
            'update_time' => array(
                'type' => 'string',  //查大于时间的用户
            ),
            'staff_id'=>array(
                'type'=>'string',
            ),
            'end_time' => array(
                'type' => 'string',
            ),
            'status' => array(   //在职状态:1在职2已离职3重新入职  默认查在职
                'type' => 'multiId',
                'default' => array(1,3),
            ),
            'name_cn' => array(
                'type' => 'string',
            ),
            'name_en' => array(
              'type'=>'string',
            ),
            'mail' => array(
                'type' => 'string',
            ),
            'match' => array(    //和name_cn或者是name_en或者mail一起使用，传'like'进行模糊查询，传'='是精确查询，默认是'='
                'type'=>'string',
                'default' => '=',
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
            'count' => array(   //当为1时获取限定条件之后的总行数
                'type' => 'integer',
            ),
            'all' => array(   //当为1时获取所有数据
                'type' => 'integer',
            ),
        );
        $this->params = $this->query()->safe();
    }

}
