<?php

// The zazzle file is important for all the module folders of zazzle. 
// It holds the includes for the module

namespace zazzle;
require_once("classes/config.php");
require_once("classes/action.php");
require_once("core/core.php");
require_once("core/registry.php");
require_once("core/action.php");
require_once("classes/hook.class.php");



use zazzle\core\coreConfig;
use zazzle\core\Access;
use zazzle\core\Registry;

Registry::register_module("core");

function load(){
	Registry::preconfig_modules();
	load_modules();
	Registry::preconfig_modules();
}


function setup(){
		echo "Loading modules<br>";
		Registry::config_modules();
		load_modules();
		echo "running Initial config<br>";
		Registry::config_modules();
}

function load_modules(){
	$root = Registry::$MODULES['core']->MODULE_ROOT;
	$folders = scandir($root);
		foreach($folders as $f){
			if(is_dir($root.$f))
				if($f != "." and $f != ".." and $f != "zazzlecore"){
					require_once($root.$f."/zazzle.php");
				}
		}
}

?>