<?php

namespace zazzle\core\registry;

class Registry{
	static $get_hooks = [];
	
	public static function get($hook){
		array_push(self::$get_hooks , $hook);
	}
}

class Event{
	public static function handle_request(){
		$requet_type = $_SERVER['REQUEST_METHOD'];
		
		$var = strtolower($requet_type)."_hooks";
		
		$request = ["request_type"=>$request_type, "request_url" => $_REQUEST['dir']];
		
		foreach(Registry::$$var as $hook){
			if($hook->test($request)){
				$hook->render($request);
			}
		}
	}
}

?>