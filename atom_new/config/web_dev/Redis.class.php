<?php

namespace Atom\Config\Web;

class Redis extends \Frame\Config {

    public static function config(){
        return array(
            'writeHost' => 'http://192.168.128.12/write',
            'xwriteHost' => 'http://192.168.128.12/xwrite',
            'readHosts' => array(

                'http://192.168.128.12:8080/read',
                'http://192.168.128.12:8080/read',

            )
        );
    }

}

