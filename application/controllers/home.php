<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Home extends CI_Controller 
	{	
		function __construct()
		{
			parent::__construct();
			
			// load the helpers
			$this->load->helper('url');
			$this->load->helper('dev');
		}
		
		// display contents of page
		function index()
		{
			$this->load->view('home_index');
		}
		
		// controller for the bitly assignment
		function bitly()
		{
			$this->load->view('bitly');
		}
		
		// controller for the internap assignment
		function internap()
		{
			$this->load->view('internap');
		}		
		
		function baublebar()
		{
			$input  = "difuhvoiwqhuv cianrivqhroiequrnh";
			$array = array();
			$answer = NULL;
			
			for($i = 0; $i < strlen($input); $i++)
			{
				if(empty($array[$input[$i]]))
				{
					$object = new stdClass();
					$object->first = $i;
					$object->latter = null;
					$array[$input[$i]] = $object;
					unset($object);
					
				} else {
						
					$older_object = $array[$input[$i]];
					$older_object->latter = $i;
					$array[$input[$i]] = $older_object;
					unset($older_object);
				}
			}
			
			// parse array
			foreach($array as $checking_object)
			{
				if(empty($checking_object->latter)){continue;}
				
				if(empty($answer))
				{
					$answer = $checking_object;
				}
				
				if($answer->first > $checking_object->first)
				{
					$answer = $checking_object;
				} 	
			}
			
			var_dump($input[$answer->first]);
		}
		
		function baublebar_r()
		{
			$array_3 = array(7,8,9);
			$array_4 = array(0, $array_3);
			$array_2 = array(4,5,$array_3);
			$array = array(1,2,3,$array_2, 10, 11);
			$array_3[] = $array_4;
			
			$final_array = $this->baublebar_hepler($array);
			var_dump($final_array);
		}
		
		function baublebar_hepler($array, $location)
		{
			$trans_array = array();
			
			if($this->check_location($location))
			{
				foreach($array as $index)
				{
					if(is_array($index))
					{
						$trans_array = array_merge($trans_array, $this->baublebar_hepler($index));
					} else {
						$trans_array[] = $index;
					}
				}
			}
			
			// recursive case
			return $trans_array;
			
		}
		
		function check_location($location)
		{
			
		}
		
		// this is a conection to the api that I manage
		function api_interface()
		{
			$this->load->view('api_display');
		}

	}
