<?php
namespace Atom\Modules\A;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Company\City;

class B extends \Frame\Module {

    private static $sample = array(
	    'city_id' => 0,
	    'city_name' => '',
    );

    public function run() {

    	$data = array(
    		'city_name' => 'cc',
    	);
    	$result = City::getInstance()->insert($data);

        if ($result === FALSE) {
        	$return = Response::gen_error(50001);
        }elseif (empty($result)) {
        	$return = Response::gen_error(30001);
        }else{
            $data['city_id'] = $result;
        	$return = Response::gen_success(Format::outputData($data, self::$sample));
        }

        $this->app->response->setBody($return);
    }

}

