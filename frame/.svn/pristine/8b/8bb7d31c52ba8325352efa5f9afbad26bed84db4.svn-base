<?php

namespace Libs\Router;

class BasicRouter {

    private $app;

    public function __construct($app) {
        $this->app = $app;
    }

    public function dispatch() {
        $path_args = $this->app->request->path_args;
		$module = array_shift($path_args);
		$action = array_shift($path_args);

        $module_namespace = $this->app->module_namespace;

        $action = $this->getRealModule($action);
		
		$module = empty($module) ? 'Index' : $module;
		$action = empty($action) ? 'Index' : $action;

		$class = $module_namespace . ucwords($module) . '\\' . ucwords($action);
        return $class;
    }

    //获取真实module
    private function getRealModule($action=''){

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
    private static function changeUnderlineTocamelCase($action=''){
        $array = explode('_', $action);
        $action = '';
        foreach ($array as $value) {
            $action .= ucwords($value);
        }
        return $action;
    }

}
