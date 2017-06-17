<?php
namespace WorkFlowApi\Modules\Bad;

use WorkFlowApi\Modules\Common\BaseModule;
use WorkFlowApi\Package\Common\Response;

class Badrequest extends BaseModule {

	public function run() {

		$this->app->response->setBody(Response::gen_error(40001));
	}
}