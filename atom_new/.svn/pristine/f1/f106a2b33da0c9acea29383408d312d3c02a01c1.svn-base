<?php

namespace Atom\Scripts\Sync;

use Atom\Package\Account\UserPrivacyInfo;
use Atom\Package\Department\DepartmentInfo;
use Atom\Package\Department\DepartmentLeader;
use Atom\Package\Migrate\Crab;
use Atom\Package\User\UserAvatar;
use Atom\Package\User\UserInfo;
use Atom\Package\User\UserPersonalInfo;
use Atom\Package\User\UserWorkInfo;

/**
 * 同步老SPEED 员工信息
 * Class SyncOldSpeedStaffInfo
 * @package Atom\Scripts\Sync
 */
class SyncOldSpeedStaffInfo extends \Frame\Script
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

        //同步头像.
        //$this->syncUpdatedAvatar($startTime);
        //$this->syncUpdatedDepart($startTime);
        $this->syncUpdatedStaff($startTime);


        return $this->response->setBody("同步结束.\n");
    }

    /**
     * 同步用户头像
     * @param $startTime
     * @throws \Exception
     */
    protected function syncUpdatedAvatar($startTime)
    {
        $avatars = Crab::model()->getUpdatedStaffAvatar($startTime);
        if (empty($avatars)) {
            return;
        }

        foreach ($avatars as $val) {
            if ($val['user_id'] < 1) {
                continue;
            }
            $src = isset($val['avatar_src_new']) && !empty($val['avatar_src_new']) ? $val['avatar_src_new'] : $val['avatar_src'];
            $a = isset($val['avatar_a_new']) && !empty($val['avatar_a_new']) ? $val['avatar_a_new'] : $val['avatar_a'];
            $b = isset($val['avatar_b_new']) && !empty($val['avatar_b_new']) ? $val['avatar_b_new'] : $val['avatar_b'];
            $c = isset($val['avatar_c_new']) && !empty($val['avatar_c_new']) ? $val['avatar_c_new'] : $val['avatar_c'];

            if (preg_match('/^\/uploads.*/', $src) && preg_match('/^pic\/.*/', $a)) { //特殊情况处理
                $src = $a;
            }

            $userAvatar = array(
                'user_id' => $val['user_id'],    //员工id
                'avatar_src' => $src, //原始头像
                'avatar_big' => $a, //200*200
                'avatar_middle' => $b, //100*100
                'avatar_small' => $c, //60*60
                'status' => 1,    //状态:0无效1有效
            );

            //删除并保存历史头像.
            UserAvatar::getInstance()->deleteLogical(array(
                'user_id' => $val['user_id'],
            ));
            //添加新头像为有效头像.
            UserAvatar::getInstance()->add($userAvatar);
        }
    }

    /**
     * 同步部门信息.
     * @param $startTime
     */
    protected function syncUpdatedDepart($startTime)
    {
        $departments = Crab::model()->getUpdatedDepart($startTime);
        if (empty($departments)) {
            return;
        }

        foreach ($departments as $depart) {
            $newDepart = array();
            $newDepart['depart_id'] = $depart['departid'];
            $newDepart['depart_name'] = $depart['departname']; //部门name
            $newDepart['depart_info'] = $depart['departinfo']; //部门信息
// 			$newDepart['depart_level'] = 'int';    //部门级别
            $newDepart['parent_id'] = $depart['father'];    //上级部门id
// 			$newDepart['child_id']     = '0'; //子部门id
// 			$newDepart['memo']         = ''; //备注
// 			$newDepart['update_time']  = date($format);
// 			$newDepart['is_official']  = 1;    //是否为正式部门:0不是1是
// 			$newDepart['is_virtual']   = 0;    //是否为虚拟部门:0不是1是
// 			$newDepart['level']        = 1;    //级别
// 			$newDepart['status']       = 1;    //状态:0无效1有效


            //department_leader
            $newDepartLeader = array(
                'depart_id' => $depart['departid'],
                'user_id' => $depart['departheader'],
            );

            $exist = DepartmentInfo::getInstance()->getByPk($depart['departid']);

            if (empty($exist)) {
                DepartmentInfo::getInstance()->add($newDepart);
            } else {
                DepartmentInfo::getInstance()->update($newDepart, array(
                    'depart_id' => $depart['departid'],
                ));
            }

            //更新部门领导.
            $exist = DepartmentLeader::getInstance()->getList(array(
                'depart_id' => $depart['departid'],
                'status' => 1,
            ));
            if (empty($exist)) {
                DepartmentLeader::getInstance()->add($newDepartLeader);
            } else {
                DepartmentLeader::getInstance()->update($newDepartLeader, array(
                    'depart_id' => $depart['departid'],
                ));
            }
        }
    }

    /**
     * 同步更新的员工信息.
     * @param $startTime
     * @throws \Exception
     */
    protected function syncUpdatedStaff($startTime)
    {
        $staffInfoList = Crab::model()->getUpdatedStaffInfo($startTime);
        if (empty($staffInfoList)) {
            return;
        }

        foreach ($staffInfoList as $staff) {

            $gender = $staff['gender'] == '男' ? 1 : 0;
            $flag = 3;
            if ($staff['level'] == 1) { //正式
                $flag = 3;
            } else if ($staff['level'] == 2) { //试用
                $flag = 2;
            } else if ($staff['level'] == 3) { //实习
                $flag = 1;
            } else if ($staff['level'] == 4) { //申请离职
                $flag = 4;
            }


            $t_user_info = array(
                'user_id' => $staff['sid'],
                //'depart_id' => $staff['departid'],    //部门id
// 				'job_role_id'   => 'int',    //职位角色
                /*
                'name_cn' => $staff['name_c'], //汉语名字
                'name_en' => $staff['name_e'], //拼音
                'mail' => strtok($staff['mail'], '@'), //邮箱前缀
                'hire_time' => $staff['hire_time'], //入职时间
                'positive_time' => $staff['positive_time'], //转正时间
                'staff_id' => $staff['staff_id'], //工号
                'gender' => $gender,    //性别0女1男
                'status' => $staff['status'],    //状态:1在职2已离职3重新入职
                */
                'flag' => $flag,             //状态:1实习2试用3正式4申请离职
            );

            $t_p_info = array(
// 				'id'             => 'int',
                'user_id' => $staff['sid'],    //员工id
                'birthday' => $staff['birth_day'], //生日
                'mobile' => $staff['phone'], //手机
// 				'mobile_another' => '', //手机
   //             'telephone' => $staff['extension'], //座机
//                'qq' => $staff['qq'], //QQ号
 //               'coat_size' => $staff['gf_size'], //上衣尺码
// 				'pants_size'     => '', //裤码
// 				'shoes_size'     => 'string', //鞋码
// 				'others'         => 'string', //其他信息 json格式存入
            );


            $t_w_info = array(
// 				'id'           => 'int',
                'user_id' => $staff['sid'],    //员工id
// 				'job_level_id' => '',    //职位级别
// 				'job_title_id' => 'int',    //职位title
//                'position' => $staff['position'], //工位
//                'redmineid' => $staff['redmineid'],    //redmine id
                //'mls_id' => $staff['mls_id'],    //美丽说id
  //              'mls_nickname' => $staff['mls_nickname'], //美丽说昵称
// 				'others'       => 'string', //其他信息 json格式存入
            );
            $t_pv_info = array(
                'user_id' => $staff['sid'],    //员工id
            );

            $userInfo = UserInfo::getInstance()->getByPk($staff['sid']);
            //新增的员工信息.
            if (empty($userInfo)) {
                $t_user_info['mail'] = strtolower($t_user_info['mail']);
                //UserInfo::getInstance()->add($t_user_info);
                //UserPersonalInfo::getInstance()->add($t_p_info);
                //UserWorkInfo::getInstance()->add($t_w_info);
                //UserPrivacyInfo::model()->insert($t_pv_info);
            } else {
                //员工信息
                $ret = UserInfo::getInstance()->updateByPk($t_user_info);
                
                $where = array(
                    'user_id' => $staff['sid'],
                );
                //个人信息
                unset($t_p_info['mobile']);
                //UserPersonalInfo::getInstance()->update($t_p_info, $where);
                //UserWorkInfo::getInstance()->update($t_w_info, $where);
             //    UserPrivacyInfo::model()->updateById($t_pv_info);
            }
        }
    }
}
