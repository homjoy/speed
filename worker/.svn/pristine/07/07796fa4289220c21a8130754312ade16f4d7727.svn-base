<?php

namespace Atom\Scripts\Test;

use Atom\Package\Notification\TemplateEngine;

class TemplateTest extends \Frame\Script {

    public function run() {
//        $data = array(
//            'username' => '111',
//            'meeting_type' => '111',
//            'meeting_topic' => '111',
//            'meeting_time' => '1111',
//            'meeting_week_day' => '星期几',
//            'zones' => array(
//                array(
//                    'place' => '广州-小蛮腰',
//                    'invite_users' => '用户1，2，3，3，4',
//                ),
//                array(
//                    'place' => '第二个地点',
//                    'invite_users' => '用户1，2，3，3，4',
//                )
//            )
//        );
//
//        $template = file_get_contents(APP_PATH .'/template/mail/meeting_create_video_sender.tpl');
//        $result = TemplateEngine::renderString($template,$data);


//        $data = array(
//            'changed_fields' => array(
//                'meeting_service',
//                'meeting_time',
//                'zones_0_place',
//            ),
//            'username' => '111',
//            'meeting_service' => '111',
//            'meeting_type' => '111',
//            'meeting_topic' => '111',
//            'meeting_time' => '1111',
//            'old_meeting_time' => '1111',
//            'meeting_week_day' => '星期几',
//            'old_meeting_week_day' => '星期几',
//            'zones' => array(
//                array(
//                    'place' => '广州-小蛮腰',
//                    'old_place' => '广州-小蛮腰',
//                    'invite_users' => '用户1，2，3，3，4',
//                ),
//                array(
//                    'place' => '第二个地点',
//                    'invite_users' => '用户1，2，3，3，4',
//                )
//            )
//        );

        $json = <<<EOF
{
"mail": {
"initiator": "潘峰",
"meeting_topic": "测试预定通知推送",
"meeting_type": "1",
"meeting_time": "2015-06-08 16:00:00",
"meeting_week_day": "星期一",
"username": "龙武亮",
"meeting_service": "投影设备",
"zones": {
"2": {
"place": "马卡龙",
"room_position": "6层进门第二间",
"invite_users": "潘峰,耿明"
}
}
}
}
EOF;

        $data = json_decode($json,true);
        $data = $data['mail'];
        try{
            $template = file_get_contents(APP_PATH .'/template/mail/meeting_create_initiator.tpl');
            $result = TemplateEngine::renderString($template,$data);
            $this->response->setBody($result);
        }catch (\Exception $e){
            var_dump($e->getMessage());
            //测试预定通知推送
        }
    }
}
