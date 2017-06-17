<?php

namespace Joint\Config\Web;

class Redis extends \Frame\Config {

    public static function config(){
        return array(
            'writeHost' => 'http://172.16.12.115:8081/write',
            'xwriteHost' => 'http://172.16.12.115:8081/xwrite',
            'readHosts' => array(

                'http://172.16.12.115:8080/read',
                'http://172.16.11.95:8080/read',
                
            )
        );
    }

}

