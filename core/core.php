<?php

namespace zazzle\core;
global $zazzle_config;

class Config{
	
	public static $CONFIG_DIR = "zazzle/zazzlecore/config2.json";
	public static $CONFIG_HASH = "";
	
	public static $MODULE_ROOT = "zazzle/";
	
	static $ACCESS_CONFIG = [];
	private static $FILE_DATA = "";
	
	static $COMMIT = false;
	
	public static function read_config(){
		if(file_exists(self::$CONFIG_DIR)){
			echo "config file exists, reading it<br>";
			self::$FILE_DATA = json_decode(file_get_contents(self::$CONFIG_DIR), true);
			echo "<table>";
			foreach(self::$FILE_DATA as $k=>$v){
				echo "<tr ><td style='border: solid 1px black'>".$k."</td><td style='border: solid 1px black'>".print_r($v, true)."</td></tr>";
				self::$$k = $v;
			}
			echo "</table>";
			Access::read_access();
		}else{
			self::$COMMIT = true;
		}

	}
	
	
	
	public static function config(){
		
		//LOAD OTHER MODULE CONFIGS HERE
		
		$folders = scandir(self::$MODULE_ROOT);
		foreach($folders as $f){
			if(is_dir(self::$MODULE_ROOT.$f))
				if($f != "." and $f != ".." and $f != "zazzlecore")
					require_once(self::$MODULE_ROOT.$f."/zazzle.php");
		}
		
		Access::check_file();
		
		if(self::$COMMIT)
			self::commit();
	}

	
	private static function commit(){

		$old_hash = self::$FILE_DATA["CONFIG_HASH"];
		
	
		$class = new \ReflectionClass('zazzle\core\Config');
		
		$data = $class->getStaticProperties();
		
		unset($data['CONFIG_HASH']);
		unset($data['FILE_DATA']);
		unset($data['COMMIT']);
		
		$new_hash = md5(json_encode($data));
		echo "Old hash: ".$old_hash."<br>";
		echo "New hash: ".$new_hash."<br>";
		echo (($old_hash == $new_hash)? "Hash match<br>" : "Hash mishmatch")."<br>";
		
		if(($old_hash == $new_hash)) return;
		
		$data['CONFIG_HASH'] = md5(json_encode($data, true));	

		file_put_contents(self::$CONFIG_DIR, json_encode($data));
	}
}

class Access{
	
	private static $File = ".htaccess";
	private static $FDATA = "";
	
	public static function read_access(){
		if(file_exists(self::$File)){
			echo "Access file exists<br>";
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
				
		if(isset(Config::$ACCESS_CONFIG[$id])){
			$array['hash'] = Config::$ACCESS_CONFIG[$id]['hash'];
		}else{
			Config::$COMMIT = true;
		}
		Config::$ACCESS_CONFIG[$id] = $array;
	}
	
	public static function unregister_access($id){
		echo "removing ".$id."<br>";
		
		$data = self::get_segment($id);
		if($data)
			Config::$CONFIG_HASH = "";
		echo "<textarea style='width:50%; height:150px;'>".self::$FDATA."</textarea>";
		self::$FDATA = str_replace($data, "" , self::$FDATA);
		unset(Config::$ACCESS_CONFIG[$id]);
		echo "<textarea style='width:50%; height:150px;'>".self::$FDATA."</textarea>";
		file_put_contents(self::$File, self::$FDATA);
		Config::$COMMIT = true;
	}
	
	static function check_file(){
		
			
		
		echo "<textarea style='width:50%; height:150px;'>".self::$FDATA."</textarea>";
		
		foreach(Config::$ACCESS_CONFIG as $a=>$v){
			echo "Looking if segment present: ".$a."<br>";
			if(!($segment = self::get_segment($a))){
				echo "-No Segment<br>";
				$item = self::build_item_data($a);
				self::$FDATA.= $item;
				Config::$ACCESS_CONFIG[$a]['hash'] = md5($item);
				Config::$COMMIT = true;
			}else{
				echo "-Segment<br>";
								
				$old_hash = Config::$ACCESS_CONFIG[$a]['hash'];
				$new_hash = md5(self::build_item_data($a));
				
				echo "Old hash: ".$old_hash."<br>";
				echo "New hash: ".$new_hash."<br>";
				
				if($old_hash == $new_hash){
					echo "-Segment corrent<br>";
				}else{
					echo "-Segment changed<br>";
					
					self::$FDATA = str_replace($segment, self::build_item_data($a), self::$FDATA);
					
					Config::$ACCESS_CONFIG[$a]['hash'] = $new_hash;
					Config::$COMMIT = true;
				}
				
			}
		}
		echo "<textarea style='width:50%; height:150px;'>".self::$FDATA."</textarea>";
		if(Config::$COMMIT)
			file_put_contents(self::$File, self::$FDATA);
		
	}

	
	static function build_item_data($id){
		
		$start_marker = "#zazzle_acc_start ".$id;
		$end_marker = "#zazzle_acc_end ".$id;
		$data = Config::$ACCESS_CONFIG[$id];
		$ret = $start_marker."\nRewritEengine on \n";
	
	
		foreach($data['cond'] as $cond=>$rules){
			foreach($rules as $rule)
			$ret .= "RewriteCond ".$cond." ".$rule."\n";
		}
		$ret .= "RewriteRule ".$data['catch']." ".$data['to']."\n".$end_marker."\n";
		return $ret;
	}
	
	static function get_segment($id){
		echo "Checking for segment: ".$id."<br>";
		$start_marker = "#zazzle_acc_start ".$id;
		$end_marker = "#zazzle_acc_end ".$id."\n";
		
		$start = strpos(self::$FDATA, $start_marker);
		$end = strpos(self::$FDATA, $end_marker);
		
		echo "--Start: ".$start."<br>";
		echo "--End: ".$end."<br>";
		
		if($start === false){
			return false;
		}
		
		$segment = substr(self::$FDATA, $start, $end - $start  +strlen($end_marker));
		echo "--Segment: ".$segment."<br>";
		return $segment;
	}
	
}


class Modules{
	
}

?>