<?php

namespace Joint\Config\Web;

class Remote extends \Frame\Config {

    public static function config()
    {
        return array(
            'atom' => array(
                'http://atom.testnewspeed.meilishuo.com',
                'http://atom.testnewspeed.meilishuo.com',
            ),
            'worker' => array(
                'http://worker.speedlab.meilishuo.com',
                'http://worker.speedlab.meilishuo.com',
            ),
        );
    }

}

