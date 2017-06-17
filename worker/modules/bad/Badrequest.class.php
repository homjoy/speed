<?php
namespace Worker\Modules\Bad;

class Badrequest extends \Frame\Module
{

    public function run()
    {
        $this->app->response->setBody(array(
            'code' => 400,
            'error_code' => 400,
            'error_msg' => 'Bad Request',
        ));
    }

}
