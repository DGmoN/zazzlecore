<?php

namespace zazzle\core\classes;

class Config{
	
	public $CONFIG_DIR = "zazzle/zazzlecore/config2.json";
	
	private $FILE_DATA = [];
		
	private $CONFIG_HASH = "";
	
	function __construct(string $file){
		$this->CONFIG_DIR = $file;
	}
	
	
	public function read_config(){
		if(file_exists($this->CONFIG_DIR)){
			//echo "config file exists, reading it<br>";
			$this->FILE_DATA = json_decode(file_get_contents($this->CONFIG_DIR), true);
			foreach($this->get_manifest() as $v){
				$this->$v = $this->FILE_DATA[$v];
			}
			
			//Access::read_access();
		}

	}
	
	
	
	public function config(){

	}
	
	function get_manifest(){
		return ["CONFIG_HASH"];
	}
	
	function get_hash_exclution(){
		return ["CONFIG_HASH"];
	}

	private function gather(){
		$ret = [];
		foreach($this->get_manifest() as $v){
			$ret[$v] = $this->$v;
		}
		return $ret;
	}
	
	function commit(){

		$old_hash = $this->FILE_DATA["CONFIG_HASH"];
		
		$data = $this->gather();
		
		foreach($this->get_hash_exclution() as $e){
			unset($data[$e]);
		}
		
		$new_hash = md5(json_encode($data));
		
		//echo "Old hash: ".$old_hash."<br>";
		//echo "New hash: ".$new_hash."<br>";
		//echo (($old_hash == $new_hash)? "Hash match<br>" : "Hash mishmatch")."<br>";
		
		if(($old_hash == $new_hash)) return false;
		
		$this->CONFIG_HASH = $new_hash;

		file_put_contents($this->CONFIG_DIR, json_encode($this->gather()));
		return true;
	}
}

?>