<?php
namespace Libs\Http;

class BasicScriptsResponse {

    private $body;

    private $status = 200;

    public $headers = array();
    /**
     * Constructor
     */
    public function __construct() {}

    public function setStatus($status)
    {
        $this->status = (int)$status;
    }

    public function setBody($content)
    {
        $this->write($content, true);
    }

    /**
     * Append HTTP response body
     * @param  string   $body       Content to append to the current HTTP response body
     * @param  bool     $replace    Overwrite existing response body?
     * @return string               The updated HTTP response body
     */
    private function write($body, $replace = false)
    {
        if ($replace) {
            $this->body = $body;
        } else {
            $this->body .= (string)$body;
        }

        return $this->body;
    }

    public function result()
    {
        return array($this->status, $this->headers, $this->body);
    }

}
