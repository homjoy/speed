<?php

namespace Frame\Middleware;

class PrettyResultHandler extends \Frame\Middleware
{
    /**
     * @var array
     */
    public function __construct() {}

    /**
     * Call
     */
    public function call()
    {
        $this->next->call();
        //Fetch status, header, and body
        list($status, $headers, $body) = $this->app->response->result();

        //Send headers
        if (headers_sent() === false) {
            if ('cli' == $this->app->sapi_type) {
                header(sprintf('Status: %s', $status));
            } else {
                header(sprintf('HTTP/%s %s', '1.1', $status));
            }
            foreach ($headers as $name => $value) {
                $hValues = explode("\n", $value);
                foreach ($hValues as $hVal) {
                    header("$name: $hVal", false);
                }
            }
        }
        echo $this->app->view->format($body);
        
        //fastcgi
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
    }

}
