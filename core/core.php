<?php

namespace zazzle\core;
use zazzle\core\classes\Config;
use zazzle\core\Registry;

class coreConfig extends Config{
		
	public $MODULE_ROOT = "";
	public $ACCESS_SCRIPT = "";
	
	public $ACCESS_CONFIG = [];
	public $ACTION_DIRS = [];
		
	function __construct(){
		$this->MODULE_ROOT = __DIR__;
		
		parent::__construct(dirname(__DIR__)."/config.json");
	}
	
	function get_manifest(){
		return ["CONFIG_HASH", "MODULE_ROOT", "ACCESS_CONFIG", "ACCESS_SCRIPT", "ACTION_DIRS"];
	}
	
	function get_hash_exclution(){
		return ["CONFIG_HASH"];
	}
		
	
	public function config(){
		if(file_exists($this->ACCESS_SCRIPT))
			require_once($this->ACCESS_SCRIPT);
		else{
			echo "No access script";
		} // Loggin that access script was not found
		
		Access::check_file();
		foreach($this->ACTION_DIRS as $act){
			if(file_exists($act))
				require_once($act);
			else{
				echo "No action script";
			}//log that action file does not exist
		}
	}
}

class Access{
	
	private static $File = ".htaccess";
	private static $FDATA = "";
	
	public static function read_access(){
		if(file_exists(self::$File)){
			//echo "Access file exists<br>";
			self::$FDATA = file_get_contents(self::$File);
		}
	}
	
	public static function register_access($id, $catch, $to, $cond = []){
		$array = [	
					"catch" 	=> $catch,
					"to"		=> $to,
					"cond"	=> $cond,
					"hash"		=> ""
				];
				
		if(isset(Registry::$MODULES['core']->ACCESS_CONFIG[$id])){
			$array['hash'] = Registry::$MODULES['core']->ACCESS_CONFIG[$id]['hash'];
		}
		Registry::$MODULES['core']->ACCESS_CONFIG[$id] = $array;
	}
	
	public static function unregister_access($id){
		//echo "removing ".$id."<br>";
		
		$data = self::get_segment($id);
		if($data)
			Registry::$MODULES['core']->CONFIG_HASH = "";
		//echo "<textarea style='width:50%; height:150px;'>".self::$FDATA."</textarea>";
		self::$FDATA = str_replace($data, "" , self::$FDATA);
		unset(Registry::$MODULES['core']->ACCESS_CONFIG[$id]);
		//echo "<textarea style='width:50%; height:150px;'>".self::$FDATA."</textarea>";
		file_put_contents(self::$File, self::$FDATA);
		Registry::$MODULES['core']->COMMIT = true;
	}
	
	static function check_file(){
		
			
		
		//echo "<textarea style='width:50%; height:150px;'>".self::$FDATA."</textarea>";
		foreach(Registry::$MODULES['core']->ACCESS_CONFIG as $a=>$v){
			//echo "Looking if segment present: ".$a."<br>";
			if(!($segment = self::get_segment($a))){
				//echo "-No Segment<br>";
				$item = self::build_item_data($a);
				self::$FDATA.= $item;
				Registry::$MODULES['core']->ACCESS_CONFIG[$a]['hash'] = md5($item);
				Registry::$MODULES['core']->COMMIT = true;
			}else{
				//echo "-Segment<br>";
								
				$old_hash = Registry::$MODULES['core']->ACCESS_CONFIG[$a]['hash'];
				$new_hash = md5(self::build_item_data($a));
				
				//echo "Old hash: ".$old_hash."<br>";
				//echo "New hash: ".$new_hash."<br>";
				
				if($old_hash == $new_hash){
					//echo "-Segment corrent<br>";
				}else{
					//echo "-Segment changed<br>";
					
					self::$FDATA = str_replace($segment, self::build_item_data($a), self::$FDATA);
					
					Registry::$MODULES['core']->ACCESS_CONFIG[$a]['hash'] = $new_hash;
					Registry::$MODULES['core']->COMMIT = true;
				}
				
			}
		}
		//echo "<textarea style='width:50%; height:150px;'>".self::$FDATA."</textarea>";

		file_put_contents(self::$File, self::$FDATA);
		
	}

	
	static function build_item_data($id){
		
		$start_marker = "#zazzle_acc_start ".$id;
		$end_marker = "#zazzle_acc_end ".$id;
		$data = Registry::$MODULES['core']->ACCESS_CONFIG[$id];
		$ret = $start_marker."\nRewritEengine on \n";
	
	
		foreach($data['cond'] as $cond=>$rules){
			foreach($rules as $rule)
			$ret .= "RewriteCond ".$cond." ".$rule."\n";
		}
		$ret .= "RewriteRule ".$data['catch']." ".$data['to']."\n".$end_marker."\n";
		return $ret;
	}
	
	static function get_segment($id){
		//echo "Checking for segment: ".$id."<br>";
		$start_marker = "#zazzle_acc_start ".$id;
		$end_marker = "#zazzle_acc_end ".$id."\n";
		
		$start = strpos(self::$FDATA, $start_marker);
		$end = strpos(self::$FDATA, $end_marker);
		
		//echo "--Start: ".$start."<br>";
		//echo "--End: ".$end."<br>";
		
		if($start === false){
			return false;
		}
		
		$segment = substr(self::$FDATA, $start, $end - $start  +strlen($end_marker));
		//echo "--Segment: ".$segment."<br>";
		return $segment;
	}
	
}


class Modules{
	
}

?>