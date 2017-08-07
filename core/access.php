<?php

namespace zazzle\core\access;

function access(){
	global $zazzle_config;
	$access_data = $zazzle_config['access'];
	$file = ".htaccess";
	
	$data = "rewriteengine on\n";
	
	foreach($access_data as $entry){
		foreach($entry['except'] as $ignore){
			$data .= "RewriteCond %{REQUEST_URI} !".$ignore."\n";
		}
		$data .= "RewriteRule ".$entry['catch']." ".$entry['to']."\n";
	}
	
	if(file_exists($file)){
		$content = get_zazzle_mod_free($file);
		$zazzle = $content.$data;
		put_htaccess($file, $data);
	}else{
		put_htaccess($file, $data);
	}
}

function get_zazzle_mod_free($file){
	$content = file_get_contents($file);
	$start = strpos($content, "#zazzle_mod_start") +strlen("#zazzle_mod_start");
	$end = strpos($content, "#zazzle_mod_end") - $start;
	$zazzle = substr($content, $start, $end);
	return str_replace($zazzle, "", $content);
}

function put_htaccess($file, $data){
	file_put_contents($file, "#zazzle_mod_start\n".$data."#zazzle_mod_end\n");
}

?>