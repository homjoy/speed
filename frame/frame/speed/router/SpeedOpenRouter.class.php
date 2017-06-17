<?php

namespace Frame\Speed\Router;

use \Frame\Speed\Router\BasicRouter;

class SpeedOpenRouter extends BasicRouter {

	public function dispatch() {
        $path_args = $this->app->request->path_args;
		
		$action = array_pop($path_args);
		$module = array_pop($path_args);
		// $folder = array_pop($path_args);
		
        $module_namespace = $this->app->module_namespace;

        $action = $this->getRealModule($action);
		
		// $folder = empty($folder) ? '' : $folder . '\\';
		$module = empty($module) ? 'Dashboard' : $module;
		$action = empty($action) ? 'Home' : $action;

		$class = $module_namespace . ucwords($module). '\\' . ucwords($action);
		
		if (!class_exists($class)) {

            $array = explode("\\",$class);
			
			$class = array_slice($array, 0, 3);
			
            $class[3] = 'Bad';
            $class[4] = 'Badrequest';

            $class = implode("\\", $class);
        }
        
        return $class;
    }
}