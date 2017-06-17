<?php
namespace Atom\Modules\Department;

use Atom\Package\Common\Response;
use Atom\Modules\Common\BaseModule;
use Libs\Util\Format;
use Atom\Package\Account\DepartmentRelation;
use Atom\Package\Account\DepartmentInfo;
use Atom\Package\Account\DepartmentSub;
use Atom\Package\Account\UserInfo;
use Atom\Package\User\UserJobRole;
use Atom\Package\Migrate\Crab;
use Frame\Speed\Exception\ParameterException;
/**
 * 获取整个部门领导
 * @author haibinzhou@meilishuo.com
 * @date 2015-08-11
 */
class GetAllDeptLeader extends BaseModule{
	
	private $params = NULL;
    private $result = array();
	public function run() {

		if(!$this->_init()) {
			$this->app->response->setBody(Response::gen_error(10001));
			return FALSE;
		}

        if(isset($this->params['user_id']) && !empty($this->params['user_id'])){ //按用户查找
            $user['user_id'] = $this->params['user_id'];
            $user_info = UserInfo::model()->getDataList($user); //获取所在部门
            if(empty($user_info)){
                throw new ParameterException("不存在此员工！");
            }
            $user_info = array_pop($user_info);
            $depart['depart_id'] = $user_info['depart_id'];   //获取部门id
        }else if(isset($this->params['depart_id']) && !empty($this->params['depart_id'])){ //按部门id查找
            $depart['depart_id'] = $this->params['depart_id'];
        }else if(isset($this->params['mail']) && !empty($this->params['mail'])){
            $mail['mail'] = $this->params['mail'];
            $user_info = UserInfo::model()->getDataList($mail); //获取所在部门
            if(empty($user_info)){
                throw new ParameterException("不存在此员工！");
            }
            $user_info = array_pop($user_info);
            $depart['depart_id'] = $user_info['depart_id'];   //获取部门id
        }

        $result = $this->leader_info($depart); //获取领导及部门信息

        if(isset($this->params['user_id']) && !empty($this->params['user_id']) && $this->params['user_id'] != 1){
            foreach($result as $key=>$val){
                if($this->params['user_id'] == $val['leader_user_id']){
                    unset($result[$key]);
                }
            }
        }
        if(isset($this->params['mail']) && !empty($this->params['mail']) && $this->params['mail'] != 'yirongxu'){
            foreach($result as $key=>$val){
                if($this->params['mail'].'@meilishuo.com' == $val['mail']){
                    unset($result[$key]);
                }
            }
        }
        $result = array_values($result);
        $result = $this->drop_repeat($result,'leader_user_id');

		if($result === FALSE) {
			$return = Response::gen_error(10004);
		}else if(empty($result)) {
			$return = Response::gen_error(50002);
		}else {
			$return = Response::gen_success(Format::outputData($result));
		}

        $this->app->response->setBody($return);
	}

    private function leader_info($depart_id, $result = array()){
        static $stop = true;
        $depart_id['is_virtual'] = array(0,1);
        $leader_id = DepartmentRelation::model()->getDataList($depart_id);  //获取部门领导
        $leader_id = array_pop($leader_id);
        $uid['user_id'] = $leader_id['user_id'];
        $leader_user_info = UserInfo::model()->getDataList($uid); //获取领导名字

        if(empty($leader_user_info)){     //如果该领导离职，找替代领导
            $sub['relation_id'] = $leader_id['relation_id'];
            $sub_info = DepartmentSub::model()->getDataList($sub);
            $sub_info = array_pop($sub_info);
            $userid['user_id'] = $sub_info['user_id'];
            $leader_user_info = UserInfo::model()->getDataList($userid);
        }
        $leader_user_info = array_pop($leader_user_info);
        $leader_depart_id['depart_id'] = $leader_id['depart_id'];
        $depart_info = DepartmentInfo::model()->getDataList($leader_depart_id); //获取部门信息
        $depart_info = array_pop($depart_info);
        $role_id['role_id'] = $leader_id['role_id'];

        $leader_data['leader_user_id'] = $leader_user_info['user_id'];    //把部门领导及部门信息组成数组
        $leader_data['leader_user_name'] = $leader_user_info['name_cn'];  //领导名字
        $leader_data['depart_id'] = $depart_info['depart_id'];  //所在部门id
        $leader_data['depart_name'] = $depart_info['depart_name'];  //所在部门名称
        $leader_data['depart_level'] = $depart_info['depart_level'];  //部门级别
        $leader_data['role_id'] = $role_id['role_id'];
        $leader_data['mail'] = $leader_user_info['mail'].'@meilishuo.com';
        if($leader_id['role_id'] == 5){   //判断是否为svp
            $leader_data['svp'] = 1;
        }

        if(isset($sub)){  //用来标识是否是替换领导
            $leader_data['sub'] = 1;
        }
        $parent_id = $leader_id['parent_relation_id'];   //上级部门id

        if($stop){
             $this->result[] = $leader_data;
        }

        if($leader_data['depart_id'] != 1){
            $departid['relation_id'] = $parent_id;
            $this->leader_info($departid,$result);
        }else if($stop){
            $depart_id['relation_id'] = 1;
            $stop = false;
            $this->leader_info($depart_id,$result);
        }

        return $this->result;
    }
    /**
     *
     * 二维数组按照嵌套中的一维数组的某个字段去除重复数组
     * @author haibinzhou@meilishuo.com
     * @data 2015-09-09
     * @params  $data 二维数组
     * @params  id    字符串
     *
     */
    public static function drop_repeat($data,$id){
        $arr_id = array();
        $arr_data = array();
        foreach($data as $key=>$value){
            if($key==0){
                $arr_id[] = $value[$id];
                $arr_data[] = $value;
            }else{
                if(!in_array($value[$id],$arr_id)){
                    $arr_id[] = $value[$id];
                    $arr_data[] = $value;
                }
            }
        }
        return $arr_data;
    }
	private function _init() {
		$this->params = $this->request->GET;

		if(empty($this->params['user_id']) && empty($this->params['depart_id']) && empty($this->params['mail'])) {
			return FALSE;
		}
		
		return TRUE;
	}

}