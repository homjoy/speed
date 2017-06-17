<?php

namespace Admin\Config\Web;

class Remote extends \Frame\Config {

    public static function config()
    {
        return array(
            'workflowatom' => array(
                'http://atomlocal.workflow.meilishuo.com',
            ),
            'mail' => array(
                'http://172.16.0.123',
            ),
			'atom'	=> array(
				'http://atomlocal.newspeed.meilishuo.com'
			),
        );
    }

}
