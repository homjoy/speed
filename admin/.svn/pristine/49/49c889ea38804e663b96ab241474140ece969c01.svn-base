<?php
namespace Admin\Modules\Structure\User;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Account\UserInfo;
use Admin\Package\Account\WorkInfo;
use Admin\Package\Account\PersonalInfo;
use Admin\Package\Department\Department;
//error_reporting(E_ALL&~E_NOTICE);//前端无法屏蔽notice
/**
 * 主页信息
 * @author hongzhou@meilishuo.com
 * @since 2015-8-10 下午12:53:13
 */
class UserHome extends BaseModule {
	
	protected $errors = NULL;
	private   $params = NULL;
    private   $page_size = 20;
    private   $all = 1;
    private   $count = 1;
    private   $depart_status = 1;
    protected $checkUserPermission = TRUE;
	public function run() {

	    $this->_init();

        //分页控制
        if(isset($this->params['page'])){
            if($this->params['page']<=0){
                $this->params['page'] =1;
            }
            $this->params['offset'] = intval($this->params['page']-1)* $this->page_size;
        }

		$user_info = UserInfo::getInstance()->getUserInfo($this->params);
        $userIds =array();
        $departIds = array();
   		if ($user_info == FALSE) {
           $return =  Response::gen_success(array());
           $return['count'] ='';
           $return['page'] ='';
           return $this->app->response->setBody($return);
	    }
        foreach ($user_info as $k => $v) {
            $userIds[] = isset($v['user_id'])?$v['user_id']:'';
        }
        $user_personal_info = PersonalInfo::getInstance()->getPersonalInfo(array('user_id' => implode(',', $userIds), 'status' => array(1,2,3),
            'all'=>$this->all));

        $user_work_info =WorkInfo::getInstance()->getWorkInfo(array('user_id' => implode(',', $userIds),'all'=>$this->all));
        $depart_info = Department::getInstance()->getDepart(array('status' =>$this->depart_status,'all'=>$this->all));

        $user_work_map = array();
        if ($user_work_info !== FALSE) {
            foreach ($user_work_info as $k => $v) {
                $user_work_map[$v['user_id']] = isset($v['mls_nickname'])?$v['mls_nickname']:'';
            }
        }
        $result = array();
        foreach ($user_info as $k => $v) {
            $res['user_id'] = isset($v['user_id'])?$v['user_id']:'';
            $res['staff_id'] = isset($v['staff_id'])?$v['staff_id']:'';
            $res['user_name'] = isset($v['name_cn'])?$v['name_cn']:'';
            $res['mail'] = isset($v['mail'])?$v['mail']:'';
            $res['depart_id'] = isset($v['depart_id'])?$v['depart_id']:'';
            $res['depart_name'] = isset($depart_info[$v['depart_id']]['depart_name'])?$depart_info[$v['depart_id']]['depart_name']:'';
            $res['mobile'] = isset($user_personal_info[$v['user_id']]['mobile'])?$user_personal_info[$v['user_id']]['mobile']:'';
            $res['qq'] = isset($user_personal_info[$v['user_id']]['qq'])?$user_personal_info[$v['user_id']]['qq']:'';
            $res['nickname'] = isset($user_work_map[$v['user_id']])?$user_work_map[$v['user_id']]:'';
            $result[] = $res;
        }

        $return = Response::gen_success($result);//返回总数
        $temp_count = UserInfo::getInstance()->getUserInfo(array('status'=>$this->params['status']
            ,'count'=>$this->count));
        $return['count'] = ceil($temp_count/$this->page_size);
        $return['page'] = $this->params['page'];
        return $this->app->response->setBody($return);

	}
	
	private function _init() {
		
		$this->rules = array(
			'status'  => array(
				'type'    => 'multiId',
				'default' => array(1,3),
			),
            'page'  => array(
                'type'    => 'integer',
                'default' => 1,
            ),
            'limit'  => array(
                'type'    => 'integer',
                'default' => 20,
            ),
            'offset'  => array(
                'type'    => 'integer',
                'default' => 0,
            ),
		);
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
	}
	
}