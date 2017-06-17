<?php

namespace Frame\Speed\Router;

class BasicRouter {

    protected $app;

    public function __construct($app) {
        $this->app = $app;
    }

    public function dispatch() {
        $path_args = $this->app->request->path_args;
		
		$action = array_pop($path_args);
		$module = array_pop($path_args);
		$folder = array_pop($path_args);
		
        $module_namespace = $this->app->module_namespace;

        $action = $this->getRealModule($action);
		
		$folder = empty($folder) ? '' : $folder . '\\';
		$module = empty($module) ? 'Index' : $module;
		$action = empty($action) ? 'Index' : $action;

		$class = $module_namespace . ucwords($folder) . ucwords($module). '\\' . ucwords($action);
		
		if (!class_exists($class)) {

            $array = explode("\\",$class);
			
			$class = array_slice($array, 0, 3);
			
            $class[3] = 'Bad';
            $class[4] = 'Badrequest';

            $class = implode("\\", $class);
        }
        
        return $class;
    }

    //获取真实module
    protected function getRealModule($action=''){

        if (!is_null($this->app->url_mode)) {
            switch ($this->app->url_mode) {
                case 'underline':
                    return self::changeUnderlineTocamelCase($action);
                    break;
                case 'camelCase':
                    return $action;
                    break;
                default:
                    break;
            }
        }
        return $action;
    }

    //下划线转为驼峰
    protected static function changeUnderlineTocamelCase($action=''){
        $array = explode('_', $action);
        $action = '';
        foreach ($array as $value) {
            $action .= ucwords($value);
        }
        return $action;
    }

}
