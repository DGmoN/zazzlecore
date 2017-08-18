<?php

namespace zazzle\core;

class Actions{
	
	public static function rest_action($url=null){
		if(!$url)
			$url = $_SERVER['REQUEST_URI'];
		
		foreach(Registry::$ACTIONS as $R=>$H){
			$matchs = [];
			if(preg_match("/".$R."/", $url, $matchs)){
				if(is_array($H)){
					foreach($H as $E)
						self::handle_action($E, $matchs);
				}else{
					self::handle_action($H, $matchs);
				}
			}
		}
	}
	
	private static function handle_action($a, $matches){

		if(is_a($a, "zazzle\core\classes\Action")){
			if(!$a->special_comparason()) return;
			$a->commit($matches);
		}else{
			$a($matches);
		}
	}
	
	
}

?>