<?php
namespace Admin\Modules\Structure\User;
use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Department\Department;
use Admin\Package\Account\UserInfo;
use Admin\Package\Account\WorkInfo;
use Admin\Package\Account\PersonalInfo;
/**
 * @author hongzhou@meilishuo.com
 * @since 2015-8-10 下午12:53:13
 */
class AjaxSearchUser extends BaseModule {

	protected $errors = NULL;
	private $params = NULL;
	public static $VIEW_SWITCH_JSON = TRUE;

	public function run() {

		$this->_init();

		//参数校验
        if($this->params['status']==''&&empty($this->params['search'])&&empty($this->params['hire_start_time'])&&empty($this->params['hire_end_time'])){
            $return = Response::gen_error(30001,  '没有要搜索信息');
            return $this->app->response->setBody($return);
        }
		//获取
         $queryParams = $user_info =$userIds = $departIds = array();
         $queryParams['hire_start_time'] = $this->params['hire_start_time'];
         $queryParams['hire_end_time'] = $this->params['hire_end_time'];
         $queryParams['all'] = $this->params['all'];
         $queryParams['status'] = $this->params['status'];

        if (!empty($this->params['search'])){
        	 if (ord($this->params['search']) <= 57) { // mobile
                  $queryParams['mobile'] = $this->params['search'];
                  $queryParams['qq'] = $this->params['search'];

                $user_personal_info = PersonalInfo::getInstance()->searchPersonalInfo($queryParams);
                $user_temp =array();
                if (is_array($user_personal_info)) {
                    foreach ($user_personal_info as $k => $v) {
                        $user_temp[] = isset($v['user_id'])?$v['user_id']:'';
                    }
					$queryParams['user_id'] = $user_temp;
                    $user_info = UserInfo::getInstance()->getUserInfo($queryParams);
                }
                if (is_array($user_info)) {
                    foreach ($user_info as $k => $v) {
                        $userIds[] = isset($v['user_id'])?$v['user_id']:'';
                        $departIds[] = isset($v['depart_id'])?$v['depart_id']:'';
                        
                    }
                    $departIds = array_unique($departIds);
                } 
            } else {

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
                    $user_personal_info = PersonalInfo::getInstance()->getPersonalInfo(array('user_id' => implode(',', $userIds)));
                }
            }
   	    } else {

   			$user_info = UserInfo::getInstance()->getUserInfo($queryParams);//这是得到信息不需要处理 $this->parseApiData()这样的处理
   			if (is_array($user_info)) {
	        	foreach ($user_info as $k => $v) {
		        			$userIds[] = isset($v['user_id'])?$v['user_id']:'';
		        			$departIds[] = isset($v['depart_id'])?$v['depart_id']:'';
	        	}
	        	$departIds = array_unique($departIds);
	        	$user_personal_info =  PersonalInfo::getInstance()->getPersonalInfo(array('user_id' => implode(',', $userIds),'all'=>$this->params['all']));
	        }
   		}
        $res = $result =array();
        if (!empty($userIds) && !empty($departIds)) {

        	$user_work_info = WorkInfo::getInstance()->getWorkInfo(array('user_id' => implode(',', $userIds),'all'=>$this->params['all']));
        	$depart_info = Department::getInstance()->getDepart(array('depart_id' => implode(',', $departIds),'all'=>$this->params['all']));

		    foreach ($user_info as $k => $v) {
		   
                    $res['user_id'] = isset($v['user_id'])?$v['user_id']:'';
                    $res['staff_id'] = isset($v['staff_id'])?$v['staff_id']:'';
                    $res['user_name'] = isset($v['name_cn'])?$v['name_cn']:'';
                    $res['mail'] = isset($v['mail'])?$v['mail']:'';
                    $res['depart_name'] = isset($depart_info[$v['depart_id']]['depart_name'])?$depart_info[$v['depart_id']]['depart_name']:'';
                    $res['qq'] = isset($user_personal_info[$v['user_id']]['qq'])?$user_personal_info[$v['user_id']]['qq']:'';
                    $res['nickname'] = isset($user_work_info[$v['user_id']]['mls_nickname'])?$user_work_info[$v['user_id']]['mls_nickname']:'';
                    $res['mobile'] = isset($user_personal_info[$v['user_id']]['mobile'])?$user_personal_info[$v['user_id']]['mobile']:'';
		      
		        $result[] = $res;
		    }
        }

        if ($result===FALSE) {
	        $return = Response::gen_error(50002);
	    } else {
	        $return = Response::gen_success($result);
	     }
   		$this->app->response->setBody($return);

	}

	private function _init() {
		
		$this->rules = array(
			'search' => array(//手机 邮箱 姓名 拼音 QQ
				'type' => 'string',
			),
			'hire_start_time' => array(
				'type' => 'datetime',
			),
			'hire_end_time' => array(
				'type' => 'datetime',
			),
			'status'  => array(
				'type'    => 'multiID',
				'default' => array(1,3),
			),
			'all'  => array(
				'type'    => 'integer',
				'default' =>1,
			),

		);

		$this->params = $this->query()->safe();
		$this->errors = $this->query()->getErrors();
	}
}
