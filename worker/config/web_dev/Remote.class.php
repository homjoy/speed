<?php

namespace Worker\Config\Web;

class Remote extends \Frame\Config {

    public function config()
    {
        return array(
            'atom' => array(
                'http://atom.testnewspeed.meilishuo.com',
                'http://atom.testnewspeed.meilishuo.com',
            ),
            'mail' => array(
                'url' => 'http://127.0.0.1',
                'method' => 'POST',
                'app_key' => '',
                'project' => 1120,
                'flag' => 1,
            ),
            'sms' => array(
                'url' => 'http://127.0.0.1/smsapi.php?smsKey=1392711224481&type=sms&',
                'method' => 'GET',
            ),
            'im' => array(
                'url' => 'http://apitest.speed.meilishuo.com/im/publicMsg',
                'method' => 'POST',
                'token' => '',
            )
        );
    }

}