<?php
namespace Atom\Modules\A;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Company\City;

class A2 extends \Frame\Module {

    private static $sample = array(
	    'city_id' => 0,
	    'city_name' => '',
    );

    public function run() {

    	//$result = City::getInstance()->getCityList(array('city_id' => 1,));
        $result = City::getInstance()->getCityById(1);

        if ($result === FALSE) {
        	$return = Response::gen_error(50001);
        }elseif (empty($result)) {
        	$return = Response::gen_error(30001);
        }else{
        	$return = Response::gen_success(Format::outputData($result, self::$sample));
        }

        $this->app->response->setBody($return);
    }

}

