<?php

namespace Admin\Config\Web;

class Remote extends \Frame\Config {

    public static function config()
    {
        return array(
            'workflowatom' => array(//workflowlab
                'http://workflowatom.testnewspeed.meilishuo.com',
            ),
            'workflowapi' => array(//workflowlab
                'http://workflowapi.testnewspeed.meilishuo.com',
            ),
            'atom' => array(
                'http://atom.testnewspeed.meilishuo.com',
                'http://atom.testnewspeed.meilishuo.com',
            ),//本地的
            'joint' => array(
                'http://joint.testnewspeed.meilishuo.com',
                'http://joint.testnewspeed.meilishuo.com',
            ),//本地的
            'worker' => array(
                'http://worker.testnewspeed.meilishuo.com',
                'http://worker.testnewspeed.meilishuo.com',
            ),//本地的
            'admin' => array(
                'http://admin.testnewspeed.meilishuo.com',
            ),
            'mail' => array(
                'http://http://yz.it.api01.meiliworks.com',
            ),

        );
    }

}
