<?php

namespace Apicloud\Scripts;

class Demo extends \Frame\Script {

    public function run() {
        $this->response->setBody("script demo");
    }
}
