<?php

namespace Atom\Config\Web;

class Remote extends \Frame\Config {

    public static function config()
    {
        return array(
            'atom' => array(
                'http://atom.test.meilishuo.com',
                'http://atom.test.meilishuo.com',
            ),
            'worker' => array(
                'http://worker.newspeed.meilishuo.com',
                'http://worker.newspeed.meilishuo.com',
            ),
        );
    }
}

