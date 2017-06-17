<?php
namespace Admin\Modules\Structure\User;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Account\RoleInfo;
use Admin\Package\Account\TitleInfo;
use Admin\Package\Department\Department;
use Admin\Package\Account\UserInfo;
use Admin\Package\Account\WorkInfo;
use Admin\Package\Account\PersonalInfo;
use Admin\Package\Account\PrivacyInfo;
/**
 * 用户信息
 * @author hongzhou@meilishuo.com
 * @since 2015-8-19 下午12:53:13
 */

class AddUser extends BaseModule {

	private   $params = NULL;
    private   $status = 1;
    private   $all = 1;
	private    $data=array();

	public function run() {

        $this->_init();

//准备加载数据
        $depart_data= Department::getInstance()->getDepart(array('status'=>$this->status
        ,'all' =>$this->all));

        $title_data=TitleInfo::getInstance()->getTitleInfo(array('status'=>$this->status
            ,'all' =>$this->all));
//        RoleInfo::getInstance()->getRoleInfo(array('status'=>$this->status
//        ,'all' =>$this->all));


//对加载数据父子关系关联
        $sub_type = array();
        if($depart_data && $title_data){ // 不为空则循环父子关系
            foreach ($depart_data as $k_parent => $v_parent) {
                foreach ($title_data as $k_list => $v_list) {
                    if ($v_list['depart_id'] == $v_parent['depart_id']) {
                        $v_list['job_title_id'] = isset($v_list['title_id'])?$v_list['title_id']:'';
                        $v_list['job_title_name'] = isset($v_list['title_name'])?$v_list['title_name']:'';
                        $sub_type[$v_parent['depart_id']][] = $v_list;
                    }
                }
            }
        }
//添加数据准备
        $all_role_info = RoleInfo::getInstance()->getRoleInfo(array('all'=>$this->all,'status'=>$this->status));
        if(empty($this->params['user_id'])){
            return $this->app->response->setBody(array(
                'parentType' => $depart_data,
                'subType'    => $sub_type,
                'data'       => $this->data,
                'roleInfo'  => $all_role_info,
            ));
        }
//修改页面准备数据
        $user_info = UserInfo::getInstance()->getUserInfo($this->params);
        $user_personal_info = PersonalInfo::getInstance()->getPersonalInfo($this->params);
        $user_work_info = WorkInfo::getInstance()->getWorkInfo( $this->params);
        $user_privacy_info = PrivacyInfo::getInstance()->getPrivacyInfo($this->params);

        $temp_data=array();
        if(is_array($user_info)&& !empty($user_info)){
            $user_info= array_pop($user_info);
            $depart_info = Department::getInstance()->getDepart(array('depart_id'=>$user_info['depart_id']));
            $user_info['depart_name'] =isset($depart_info[$user_info['depart_id']]['depart_name'])?$depart_info[$user_info['depart_id']]['depart_name']:'';
            $role_info  = RoleInfo::getInstance()->getRoleInfo(array('role_id'=>$user_info['job_role_id']));
            $user_info['job_role_name'] =isset($role_info[$user_info['job_role_id']]['role_name'])?$role_info[$user_info['job_role_id']]['role_name']:'';
           if(isset($user_info['direct_leader'])){//以后删除兼容老系统处理
               $old_leader = UserInfo::getInstance()->getUserInfo(array('user_id'=>$user_info['direct_leader']));
               $old_leader= is_array($old_leader)?array_pop($old_leader):'';
               $user_info['direct_name']   = isset($old_leader['name_cn'])?$old_leader['name_cn']:'';
           }
            $temp_data = array_merge($user_info,$temp_data);
        }
        if(is_array($user_personal_info)&& !empty($user_personal_info)){
            $user_personal_info= array_pop($user_personal_info);
            if(isset($user_personal_info['others'])){
                $user_personal_info['others_pinfo'] =$user_personal_info['others'];
                unset($user_personal_info['others']);
            }
            $temp_data = array_merge($user_personal_info,$temp_data);
        }
        if(is_array($user_work_info)&& !empty($user_work_info)){
            $user_work_info= array_pop($user_work_info);
            if(isset($user_work_info['others'])){
                $user_work_info['others_work'] =$user_work_info['others'];
                unset($user_work_info['others']);
            }
            $temp_data = array_merge($user_work_info,$temp_data);
        }
        if(is_array($user_privacy_info)&& !empty($user_privacy_info)){
            $user_privacy_info= array_pop($user_privacy_info);
            $temp_data = array_merge($user_privacy_info,$temp_data);
        }


	     $this->app->response->setBody(array(
             'parentType' => $depart_data,
             'subType'    => $sub_type,
             'data'       => array_merge($this->data,$temp_data),
             'roleInfo'  => $all_role_info,
         ));

	}
	private function _init() {

		$this->rules = array(
			'user_id' => array(
				'type'=>'integer',
			)
		);
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
	}
	
}