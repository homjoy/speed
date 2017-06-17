<?php

namespace WorkFlowApi\Config\Web;

class Remote extends \Frame\Config {

    public static function config()
    {
        return array(
            'atom' => array(
                'http://atom.testnewspeed.meilishuo.com',
                'http://atom.testnewspeed.meilishuo.com',
            ),
            'workflowatom' => array(
                'http://workflowatom.testnewspeed.meilishuo.com',
            ),
        );
    }

}

