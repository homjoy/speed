<?php
namespace Atom\Modules\Department;

use Atom\Package\Common\Response;
use Atom\Modules\Common\BaseModule;
use Libs\Util\Format;
use Atom\Package\Account\DepartmentRelation;
use Atom\Package\Account\DepartmentInfo;
use Atom\Package\Account\DepartmentSub;
use Atom\Package\Account\UserInfo;
use Atom\Package\Account\UserAvatar;
use Libs\Util\ArrayUtilities;
/**
 * 获取直属领导
 * @author haibinzhou@meilishuo.com
 * @date 2015-08-11
 */
class GetDirectLeader extends BaseModule{
	
	private $params = NULL;
    private $relation = array();

	public function run() {

		if(!$this->_init()) {
			$this->app->response->setBody(Response::gen_error(10001));
			return FALSE;
		}
        $result  = array();
        //新库是使用
        if(isset($this->params['user_id']) && !empty($this->params['user_id'])){ //按用户查找
            $user_info = UserInfo::model()->getDataList($this->params,0,9999);
            if(!empty($user_info)){
                foreach($user_info as $key=>$value){
                    $depart['depart_id'] = $value['depart_id'];
                    $leader = $this->getLeaderId($depart,$key);
                    $result[$key] = array_pop($leader);
                }
            }
        }else if(isset($this->params['mail']) && !empty($this->params['mail'])){
            $mail['mail']   = $this->params['mail'];
            $mail['status'] = array(1,2,3);
            $user_info = UserInfo::model()->getDataList($mail);
                $user_info = array_pop($user_info);
                if($user_info['status'] == 2 || empty($user_info)){
                    $result[$user_info['user_id']] = array();
                }else{
                    $uid = $user_info['user_id'];
                    $depart['depart_id'] = $user_info['depart_id'];
                    $result = $this->getLeaderId($depart,$uid);
                }
        }

        $result = $this->getUserAvatar($result);

		if($result === FALSE) {
			$return = Response::gen_error(10004);
		}else {
			$return = Response::gen_success(Format::outputData($result));
		}

        $this->app->response->setBody($return);
	}


    //获取所有的relation领导
    private function getLeaderId($depart_id,$uid){
        $depart = DepartmentRelation::model()->getDataList($depart_id);
        $leader_user_info = array();
        $relation_data = array();
        if(!empty($depart)){
            $depart = array_pop($depart);
            if($depart['relation_id'] != 1){
                $relation['relation_id'] = $depart['parent_relation_id'];
                $relation['is_virtual']  = array(0,1);
                $relation_data[] = $depart;
                $allRelation = $this->parent_relation($relation,$relation_data);

                foreach($allRelation as $k=>&$value){
                    if($value['user_id'] == 0){
                        $sub['relation_id'] = $value['relation_id'];
                        $this->sub_leader($sub,$value,$uid);
                    }else if($value['user_id'] == $uid){
                        unset($allRelation[$k]);
                    }
                }

                //领导基本信息
                $leader_user_info = $this->user_info($allRelation);
            }else{
                //直接就是易容
                $CEO[] = $depart;
                $leader_user_info = $this->user_info($CEO);
            }
        }

        $leader_user_info = $this->drop_repeat($leader_user_info,'id');
        $leader = array();

        if(!empty($leader_user_info)){
            $leader[$uid] = $leader_user_info[0];
            $leader[$uid]['direct_leader'] = isset($leader_user_info[1]) ? $leader_user_info[1]['id'] : $leader_user_info[0]['id'];
        }

        return $leader;
    }

    //获取所有上级，直到易容
    private function parent_relation($params,&$relation_data){
        $parent_relation = DepartmentRelation::model()->getDataList($params);
        $parent_relation = array_pop($parent_relation);
        $relation_data[] = $parent_relation;
        if($parent_relation['relation_id'] != 1){
            $param_relation['relation_id'] = $parent_relation['parent_relation_id'];
            $param_relation['is_virtual'] = array(0,1);
            $this->parent_relation($param_relation,$relation_data);
        }

        return $relation_data;
    }
//获取领导基本信息
    private function user_info($user){
        $result = array();
        foreach($user as $info){
            if(isset($info['user_id']) && $info['user_id'] != 0){
                $u_id['user_id'] = $info['user_id'];
                $user_info = UserInfo::model()->getDataList($u_id);
                if(!empty($user_info)){
                    $user_info = array_pop($user_info);
                    $departid['depart_id'] = $user_info['depart_id'];
                    $depart_info = DepartmentInfo::model()->getDataList($departid);
                    $depart_info = array_pop($depart_info);

                    $result[$info['relation_id']]['id'] = $user_info['user_id'];
                    $result[$info['relation_id']]['name'] = $user_info['name_cn'];
                    $result[$info['relation_id']]['mail'] = $user_info['mail'].'@meilishuo.com';
                    $result[$info['relation_id']]['departid'] = $depart_info['depart_id'];
                    $result[$info['relation_id']]['depart'] = $depart_info['depart_name'];
                }
            }
        }

        return $result;
    }
    //获取代理领导
    private function sub_leader($param_sub,&$sub,$uid){
        $sub_info = DepartmentSub::model()->getDataList($param_sub);

        if(!empty($sub_info)){
            $sub_info = array_pop($sub_info);
            if($sub_info['user_id'] != $uid && $uid != 1){
                $sub['sub'] = 1;
                $sub['user_id'] = $sub_info['user_id'];
            }else{ //如果代理领导还是自己，则过滤
                $sub = array();
            }
        }else{
            $sub['whithout'] = 1;//领导离职还没有代理领导
        }

        return $sub;
    }


    //获取用户头像
    private function getUserAvatar($params = array()){
        $id['user_id'] = array_unique(ArrayUtilities::my_array_column($params,'id'));
        $user_avatar = UserAvatar::model()->getDataList($id,0,9999);

        $user_avatar = ArrayUtilities::hashByKey($user_avatar,'user_id');

        foreach($params as $key=>&$value){
            if(empty($value)){
                continue;
            }
            if(isset($user_avatar[$value['id']])){
                $value['avatar_origin'] = BEANSDB_DOMAIN.$user_avatar[$value['id']]['avatar_src'];
                $value['avatar_big'] = BEANSDB_DOMAIN.$user_avatar[$value['id']]['avatar_big'];
                $value['avatar_middle'] = BEANSDB_DOMAIN.$user_avatar[$value['id']]['avatar_middle'];
                $value['avatar_small'] = BEANSDB_DOMAIN.$user_avatar[$value['id']]['avatar_small'];

            }
            if(!isset($params[$key]['avatar_origin'])){
                $value['avatar_big'] = DEFAULT_PHOTO;
                $value['avatar_origin'] = DEFAULT_PHOTO;
                $value['avatar_middle'] = DEFAULT_PHOTO;
                $value['avatar_small'] = DEFAULT_PHOTO;
            }

        }
        return $params;
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

        return array_values($arr_data);
    }


    private function _init() {
        $this->rules = array(
            'user_id'=>array(
                'type'=>'multiId',
            ),
            'mail'=>array(
                'type'=>'string',
            ),
        );
		$this->params = $this->query()->safe();

		if(empty($this->params['user_id']) && empty($this->params['mail'])) {
			return FALSE;
		}
		return TRUE;
	}

}