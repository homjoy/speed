<?php
namespace Atom\Modules\Bad;

use Atom\Package\Common\Response;

class Badrequest extends \Frame\Module {

	public function run() {

		$return = Response::gen_error(40001);

		$this->app->response->setBody($return);

	}

}
