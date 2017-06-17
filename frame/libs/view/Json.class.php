<?php

namespace Libs\View;

class Json extends View {

    public function format($body) {
        return json_encode($body);
    }

}
