<?php
namespace Apicloud\Package\Account;



use Libs\Util\ArrayUtilities;
class CheckDomainNickname extends \Apicloud\Package\Common\BasePackage{

    private static $instance = null;

    private function __construct() {}

    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }



} 
