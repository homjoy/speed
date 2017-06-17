<?php
namespace Frame\Speed\Http;

use \Frame\Speed\Http\HttpResponse;

/**
 *
 * Class HttpAdminResponse
 * @package Frame\Speed\Http
 */
class HttpAdminResponse extends HttpResponse{
	
	protected $body = array();

    public function setBody($content) {
		
		$content = array_merge($this->body, $content);
		
        parent::setBody($content);
    }
	
	public function __set($property_name, $value) {
		$this->body[$property_name] = $value;
	}
	
	public function __get($property_name) {
		return $this->body[$property_name];
	}

}