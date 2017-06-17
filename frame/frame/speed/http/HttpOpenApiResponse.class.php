<?php
namespace Frame\Speed\Http;

use \Frame\Speed\Http\HttpResponse;

/**
 *
 * Class HttpAdminResponse
 * @package Frame\Speed\Http
 */
class HttpOpenApiResponse extends HttpResponse{
	
	protected $body = array();

    public function setBody($content) {
		
		if (!empty($content['error_code'])) {
			$content = $content;
		} else if (!empty($this->body['error_code'])) {
			$content = $this->body;
		} else {
			$content = array_merge($this->body, $content);
		}
		
        parent::setBody($content);
    }
	
	public function __set($property_name, $value) {
		$this->body[$property_name] = $value;
	}
	
	public function __get($property_name) {
		return $this->body[$property_name];
	}
}