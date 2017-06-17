<?php

namespace Atom\Scripts\Sync;

use Atom\Package\Account\UserAvatar;
use Atom\Package\Migrate\Crab;
/**
 * 同步老SPEED 员工信息
 * Class SyncOldSpeedStaffInfo
 * @package Atom\Scripts\Sync
 */
class SyncOldSpeedUserAvatarUpdate extends \Frame\Script{
    public function run(){

        $day = date('Y-m-d',strtotime("-1 day"));

        $user_avatar = UserAvatar::model()->getDataList(array('update_time'=>$day),0,10000);

        foreach($user_avatar as $val){
            $params = array(
                'user_id' => $val['user_id'],
                'update_time' => $val['update_time'],
                'avatar_src_new' => $val['avatar_src'],
                'avatar_a_new' => $val['avatar_big'],
                'avatar_b_new' => $val['avatar_middle'],
                'avatar_c_new' => $val['avatar_small'],
            );
            $old_avatar = Crab::model()->getUserAvatar($val['user_id']);
            if(!empty($old_avatar)){
                Crab::model()->updateAvatar($params);
            }else{
                Crab::model()->insertAvatar($params);
            }
        }

        return $this->response->setBody("同步结束.\n");
    }

}
