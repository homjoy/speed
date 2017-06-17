<?php

namespace Apicloud\Config\Web;

class Remote extends \Frame\Config {

    public static function config()
    {   
        return array(
            'atom' => array(
                'http://atom.newspeed.meilishuo.com',
                'http://atom.newspeed.meilishuo.com',
            ),
            'worker' => array(
                'http://worker.newspeed.meilishuo.com',
                'http://worker.newspeed.meilishuo.com',
            ),
            'mail' => array(
                'http://172.16.0.123',
            ),
            'virus' => array(
                'http://virus.meilishuo.com',
            ),
        ); 
    }

}

