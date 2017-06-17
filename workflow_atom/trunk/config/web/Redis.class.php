<?php

namespace WorkFlowAtom\Config\Web;

class Redis extends \Frame\Config {

    public function config(){
        return array(
            'writeHost' => 'http://127.0.0.1:6379/write',
            'xwriteHost' => 'http://127.0.0.1:6379/xwrite',
            'readHosts' => array(

                'http://127.0.0.1:6379/read',
                'http://127.0.0.1:6379/read',

            )
        );
//        return array(
//            'writeHost' => 'http://172.16.12.115:8081/write',
//            'xwriteHost' => 'http://172.16.12.115:8081/xwrite',
//            'readHosts' => array(
//
//                'http://172.16.12.115:8080/read',
//                'http://172.16.11.95:8080/read',
//
//            )
//        );

    }

}

