<?php
namespace Admin\Modules\Structure\Depart;
use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Department\Department;
use Admin\Package\Account\UserInfo;
/**用户搜所
 * @author hongzhou@meilishuo.com
 * @since 2015-9-25 下午12:53:13
 */
class AjaxSearchName extends BaseModule {

    protected $errors = NULL;
    private $params = NULL;
    public static $VIEW_SWITCH_JSON = TRUE;

    public function run() {

        $this->_init();
        if( $this->query()->hasError()){
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }
        //参数校验

        if(empty($this->params['search'])){
            $return = Response::gen_error(30001,  '没有要搜索信息');
            return $this->app->response->setBody($return);
        }
        //获取
        $queryParams = $user_info =$userIds = $departIds = array();
        $queryParams['all'] = $this->params['all'];
        $queryParams['status'] = $this->params['status'];


        if (ord($this->params['search']) <= 122) {

            $is_mail = preg_match('/@/', $this->params['search']);

            if ($is_mail) { // 邮箱
                $this->params['search'] = explode("@",$this->params['search']);
                $queryParams['mail']  = $this->params['search'][0];
            } else { // 英文名
                $queryParams['name_en'] = $this->params['search'];
            }
        } else { // 中文名
            $queryParams['name_cn'] = $this->params['search'];
        }
        //获取用户ID
        $user_info = UserInfo::getInstance()->searchUserInfo($queryParams);
        if (is_array($user_info)) {
            foreach ($user_info as $k => $v) {
                $userIds[] = isset($v['user_id'])?$v['user_id']:'';
                $departIds[] = isset($v['depart_id'])?$v['depart_id']:'';
            }
            $departIds = array_unique($departIds);
        }



        if (!empty($userIds) && !empty($departIds)) {

            $depart_info = $this->getDepartInfo(array('depart_id' => implode(',', $departIds),
                'all'=>1,'type'=>$this->params['type']));
            $res = $result = array();
            foreach ($user_info as $k => $v) { //拼接
                $result['user_id'] = isset($v['user_id'])?$v['user_id']:'';
                $result['mail'] = isset($v['mail'])?$v['mail']:'';
                $result['name_cn'] = isset($v['name_cn'])?$v['name_cn']:'';
                $result['depart_name'] = isset($depart_info[$v['depart_id']]['depart_name'])?$depart_info[$v['depart_id']]['depart_name']:'';
                $result['name'] = NULL;
                $result['name'] .= $result['depart_name'];
                if($result['mail']){
                    if($result['name']){
                        $result['name'] .='-';
                    }
                    $result['name'] .=$result['mail'];
                }
                if($result['name_cn']){
                    if( $result['name']){
                        $result['name'] .='-';
                    }
                    $result['name'] .=$result['name_cn'];
                }
                unset($result['name_cn']);
                unset( $result['depart_name']);
                $res[] = $result;
            }

         $this->app->response->setBody(Response::gen_success($res));
        }
    }


    private function getDepartInfo($param) {
        if($param['type']==1){//从表
            $ret = Department::getInstance()->getDepartTemp($param);
        }else{//主表
            $ret = Department::getInstance()->getDepart( $param);
        }


        return $ret;
    }


    private function _init() {

        $this->rules = array(
            'search' => array(// 邮箱 姓名 拼音
                'required'   => TRUE,
                'allowEmpty' => FALSE,
                'type' => 'string',
            ),
            'status'  => array(
                'type'    => 'multiID',
                'default' => array(1,2,3),
            ),
            'all'  => array(
                'type'    => 'integer',
                'default' =>1,
            ),
            'type'  => array(
                'type'    => 'integer',
                'enum' =>array(1,2),
                'default' =>2,//搜索主表
            ),

        );

        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }
}
