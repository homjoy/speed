<?php

namespace Atom\Scripts\Sync;
use Atom\Package\Migrate\Crab;
use Atom\Package\Account\UserInfo;
use Atom\Package\Account\UserPersonalInfo;
use Atom\Package\Account\UserWorkInfo;

/**
 * 同步新SPEED 员工信息
 * Class SyncNewSpeedStaffInfo
 * @package Atom\Scripts\Sync
 */
class SyncNewSpeedStaffInfo extends \Frame\Script
{

    /**
     * 同步的间隔数据,五分钟同步一次.
     * @var int
     */
    protected $interval = 300;


    public function run()
    {

        $now = time();
        $startTime = date('Y-m-d H:i:s', $now - $this->interval);

        $this->syncUpdatedStaff($startTime);
        return $this->response->setBody("同步结束.\n");
    }

    /**
     * 同步更新的员工信息.
     * @param $startTime
     * @throws \Exception
     */
    protected function syncUpdatedStaff($startTime)
    {
        $staffInfoList = UserInfo::model()->getDataList(array('update_time'=>$startTime,'all'=>1,'status'=>array(1,2,3)));

        foreach ($staffInfoList as $staff) {

            $gender = $staff['gender'] == '1' ? '男' : '女';


//基本信息
            if($staff['status']==1 ||$staff['status']==3){
                $staff['status']=1;
            }
            $t_user_info = array(
                'sid' => $staff['user_id'],
                'departid' => $staff['depart_id'],    //部门id
                'name_c' => $staff['name_cn'], //汉语名字
                'name_e' => $staff['name_en'], //拼音
                'mail' => $staff['mail'].'@meilishuo.com', //邮箱前缀
                'hire_time' => $staff['hire_time'], //入职时间
                'positive_time' => $staff['positive_time'], //转正时间
                'staff_id' => $staff['staff_id'], //工号
                'gender' => $gender,    //性别0女1男
                'status' => $staff['status'],    //状态:1在职2已离职3重新入职

            );
            //direct_leader处理
            if(isset($staff['direct_leader'])&& !empty($staff['direct_leader'])){
                $t_user_info['direct_leader'] =$staff['direct_leader'];
            }
            $p=array('user_id'=>$staff['user_id'],'status'=>array(1,2));
            $isAdd =Crab::model()->getStaffInfoByIds($p);//走底层海滨逻辑
            if(empty($isAdd)){
                //flag处理
                $level = 1; //状态:1实习2试用3正式4申请离职
                if ($staff['flag'] == 1) { //正式
                    $level = 3;
                } else if ($staff['flag'] == 2) { //试用
                    $level = 2;
                } else if ($staff['flag'] == 3) { //实习
                    $level = 1;
                } else if ($staff['flag'] == 4) { //申请离职
                    $level = 4;
                }
                $t_user_info['level'] =$level;
                $t_user_info['birth_day'] ='0000-00-00';
                Crab::model()->insertStaff($t_user_info);
            }else{
                Crab::model()->updateStaff($t_user_info);
            }
        }
//工作信息
        $workInfoList = UserWorkInfo::model()->getDataList(array('update_time'=>$startTime,'all'=>1));
        foreach ($workInfoList as $staff) {
            $t_work_info = array(
                'sid' => $staff['user_id'],
                'position' => $staff['position'],
                'redmineid' => $staff['redmineid'],
                'mls_id' => $staff['mls_id'],
                'mls_nickname' => $staff['mls_nickname'],


            );
            Crab::model()->updateStaff($t_work_info);

        }
//私人信息
        $personalInfoList = UserPersonalInfo::model()->getDataList(array('update_time'=>$startTime,'all'=>1));
        foreach ($personalInfoList as $staff) {

            $t_personal_info = array(
                'sid' => $staff['user_id'],
                'birth_day' => $staff['birthday'],
                'phone' => $staff['mobile'],
                'extension' => $staff['telephone'],
                'qq' => $staff['qq'],
                'gf_size' => $staff['coat_size'],

            );
            Crab::model()->updateStaff($t_personal_info);
        }
    }
}
