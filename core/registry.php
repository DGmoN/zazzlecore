<?php

namespace zazzle\core;

class Registry{

	public static $MODULES = [];
	
	public static $ACTIONS = [];
	
	public static function register_module($Name){
		echo "Module registered ".$Name."<br>";
		array_push(self::$MODULES, $Name);
	}
	
	public static function config_modules(){
		self::preconfig_modules();
		foreach(self::$MODULES as $k => $M){
			if(!is_string($M)){
				$M->config();
				echo $k.($M->commit()? " has updated its config" : " has not changed")."<br>";
			}
		}
	}
	
	public static function preconfig_modules(){
		foreach(self::$MODULES as $v=>$M){
			if(is_string($M)){
				$str = "\zazzle\\".$M."\\".$M."Config";
				$cfg = new $str();
				$cfg->read_config();
				$cfg->config();
				self::$MODULES[$M] = $cfg;
			}
		}
	}
	
	// Binds the handler(function or handler object) to a regex
	public static function register_action($regex, $handler){
		if(isset(self::$ACTIONS[$regex])){
			echo $regex." expanded action <br>";
			if(is_array(self::$ACTIONS[$regex])){
				array_push(self::$ACTIONS[$regex], $handler);
			}else{
				self::$ACTIONS[$regex] = [self::$ACTIONS[$regex], $handler];
			}
		}else{
			echo $regex." Registered action <br>";
			self::$ACTIONS[$regex] = $handler;
		}
	}
}


?>