<?php
if(!function_exists('classAutoLoader')){
	
	function classAutoLoader($class){
		$class=strtolower($class);
		$classFile=$_SERVER['DOCUMENT_ROOT'].'/classes';
		if(is_file($classFile)&&!class_exists($class)) include $classFile;


	}
}