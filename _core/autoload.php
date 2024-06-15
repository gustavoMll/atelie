<?php
spl_autoload_register(function ($class)
{
	$class = strtolower($class).'.class.php';
	$directorys = array('basics/','dao/','',);
	foreach($directorys as $directory){
	        $file = __DIR__."/model/{$directory}{$class}";
	        if(file_exists($file)){
	        	require_once($file);
	            return;
	        }
	}
    return;
});