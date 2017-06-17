<?php

namespace Worker\Config\Web;

class Redis extends \Frame\Config {

    public function config(){
        return array(
            'writeHost' => 'http://127.0.0.1:6379/write',
            'xwriteHost' => 'http://1127.0.0.1:6379/xwrite',
            'readHosts' => array(

                'http://127.0.0.1:6379/read',
                'http://127.0.0.1:6379/read',
                
            )
        );
    }

}

