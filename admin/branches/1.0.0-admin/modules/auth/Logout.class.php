<?php
namespace Admin\Modules\Auth;

use Admin\Modules\Common\BaseModule;

class Logout extends BaseModule {

	public function run() {
        \Admin\Modules\Common\BaseSession::Singleton()->destory();
        header('Location: /');
        exit();
	}

}
