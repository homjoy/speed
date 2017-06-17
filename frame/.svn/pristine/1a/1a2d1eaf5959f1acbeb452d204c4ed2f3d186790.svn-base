<?php

class PsrAutoloader {

	/**
	 * Singleton.
	 */
	public static function register($root_path_setting) {
		static $singleton = NULL;
		is_null($singleton) && $singleton = new self($root_path_setting);
		return $singleton;
	}

	/**
	 *
	 * @var string
	 */
	private $root_path_setting = array();

	private function __construct($root_path_setting) {
		$this->root_path_setting = $root_path_setting;
		spl_autoload_register(array($this, 'autoload'));
	}

	/**
	 * Get file path of source file.
	 */
	private function getFilepath($class_name) {
		// class name contains namespaces
		$pieces = explode('\\', $class_name);

        $root_path = $this->root_path_setting['default'];
		if (isset($this->root_path_setting[$pieces[0]])) {
			$root_path = $this->root_path_setting[$pieces[0]];
		}
		// get rid of the leading root namespace
		array_shift($pieces);

		$class_name = array_pop($pieces);
		$base_path = function () use ($root_path, $pieces) {
			return empty($pieces) ? $root_path : $root_path . DIRECTORY_SEPARATOR . strtolower(implode(DIRECTORY_SEPARATOR, $pieces));
		};

        //psr0
		return str_replace('_', DIRECTORY_SEPARATOR, $base_path() . "/{$class_name}.class.php");
	}

	/**
	 * The method that actually triggers require().
     * require() is better then require_once()
	 */
    private function autoload($class_name) {
        $filepath = $this->getFilepath($class_name);
        //echo $class_name . "\t" . $filepath . "\n";
        file_exists($filepath) && $this->onceRequire($filepath);
    }

	private function onceRequire($filepath) {
		static $files = array();
		isset($files[$filepath]) or require $filepath;
		$files[$filepath] = 1;
	}

}
