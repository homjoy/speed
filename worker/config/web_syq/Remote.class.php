<?php

namespace Worker\Config\Web;

class Remote extends \Frame\Config {

    public function config()
    {
        return array(
            'atom' => array(
                'http://atom.newspeed.meilishuo.com',
                'http://atom.newspeed.meilishuo.com',
            ),
            'mail' => array(
                'url' => 'http://mail01.meilishuo.com/officesendmail',
                'method' => 'POST',
                'app_key' => '523943fd9a548',
                'project' => 1120,
                'flag' => 1,
            ),
            'sms' => array(
                'url' => 'http://smsapi.meilishuo.com/smssys/interface/smsapi.php?smsKey=1392711224481&type=sms&',
                'method' => 'GET',
            ),
            'im' => array(
                'url' => 'http://api.speed.meilishuo.com/im/publicMsg',
                'method' => 'POST',
                'token' => '',
            )
        );
    }

}