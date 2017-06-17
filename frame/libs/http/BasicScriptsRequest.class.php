<?php
namespace Libs\Http;

class BasicScriptsRequest {

	protected $request_data;

	public function __construct() {
		// initialize request data
        $this->request_data['arg'] = $this->getScriptsCommands();

	}

	public function __get($name) {
		if (!isset($this->request_data[$name])) {
			return NULL;
		}
		return $this->request_data[$name];
	}

    private function getScriptsCommands() {
        global $argv;
        //pop the script file name
        array_shift($argv);
        $name = array_shift($argv);
        return array('name' => $name, 'arg' => $argv);
    }

    public function __toString() {
        return serialize($this->request_data);
        //return json_encode($this->request_data);
    }

}
