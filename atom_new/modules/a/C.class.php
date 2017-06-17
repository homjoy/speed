<?php
namespace Atom\Modules\A;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Company\City;

class C extends \Frame\Module {

    private static $sample = array(
	    'city_id' => 0,
	    'city_name' => '',
    );

    public function run() {

    	$data = array(
    		'city_name' => 'aa',
            'city_id' => 11,
    	);
        $result = City::getInstance()->update($data);

        if ($result === FALSE) {
        	$return = Response::gen_error(50001);
        }elseif (empty($result)) {
        	$return = Response::gen_error(50012);
        }else{
        	$return = Response::gen_success(Format::outputData($data, self::$sample));
        }

        $this->app->response->setBody($return);
    }

}

