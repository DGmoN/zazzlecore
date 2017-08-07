<?php

namespace zazzle\classes;

class Hook{
	
	protected $id;
	function __construct($id){
		$this->id =$id;
		
	}
	
	function test(&$request){
		return false;
	}
	
	function render(&$data){
		
	}
}

class URL_hook extends Hook{
	
	private $regex;
	
	function __construct(string $regex, $id){
		parent::__construct($id);
		$this->regex = $regex;
	}

	function test(&$request){
		$matches = array();
		if(($opt = preg_match($this->regex, $request['url'], $matches))){
			if(!isset($request['pregmatch'])){
				$request['pregmatch'] = [];
			}
			$request['pregmatch'][$this->id] = $opt;
			return true;
		}
		return false;
	}
	
	function render(&$data){
		
	}
}

?>