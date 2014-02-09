<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');  

	function debug($value = NULL, $name = NULL, $exit = NULL)
	{
		if(!empty($name))
		{
			echo $name . "<br />";				
		}
		echo "<pre>";
		var_dump($value);
		echo "</pre>";
		
		if($exit)
		{
			exit;
		}
	}