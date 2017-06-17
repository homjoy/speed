<?php

namespace Frame\Speed\View;
use Libs\Blade\Blade;

class View {

    protected $app;

    public function __construct($app) {
        $this->app = $app;
    }

    public function format($data = array()) {
        if(OA_VIEW_SWITCH != 'ON'){
            return $data;
        }
		
		$class = $this->app->router->dispatch();
		$class = $this->checkClass($class);
		
		
		if(isset($class::$VIEW_SWITCH_JSON) && $class::$VIEW_SWITCH_JSON === TRUE) {
			//输出 json 数据
			$json_view =  new \Frame\Speed\View\Json($this->app);
			return $json_view->format($data);
			
		}
		
		$class = str_replace("\\Admin\\Modules\\", '', $class);
		$class = explode("\\", strtolower($class));
		$class[count($class) - 1] = ucwords($class[count($class) - 1]);
		
		$dispatch = array();
		
		$path = '';
		if(count($class) == 3) {
			$path = $class[0];
			
			$dispatch['dispathModule'] = $class[1];
			$dispatch['dispathAction'] = $class[2];
		}else {
			$dispatch['dispathModule'] = $class[0];
			$dispatch['dispathAction'] = $class[1];
		}
		
		$template = implode('/', $class);
		
//    	$path_args = $this->app->request->path_args;
//		$module = array_shift($path_args);
//		$action = array_shift($path_args);
//
//		$module_namespace = $this->app->module_namespace;
//
//        empty($module) && $module = 'bad';
//        empty($action) && $action = 'badrequest';

		
        $views = APP_PATH . 'views';
        $cache = APP_PATH . 'cache/' . $path;

        if(!file_exists($cache)){
            mkdir($cache, 0777, true);
        }

        $blade = new Blade($views, $cache);
        //$template = $module .  "/" . ucwords(strtolower($action));
		
		if(!is_array($data)) {
			$data = array(
				'template_value'	=> $data
			);
		}
		$data = array_merge($data, $dispatch);

        echo $blade->view()->make($template, $data, array())->render();

    }
	
	private function checkClass($class){
        if (!class_exists($class)) {

            $array = explode("\\",$class);
            $array[3] = 'Bad';
            $array[4] = 'Badrequest';

            $class = implode("\\", $array);
        }
        return $class;
    }

}