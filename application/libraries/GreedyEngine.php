<?php if( !defined('BASEPATH')) exit('No direct script access allowed');

	class GreedyEngine {
		
		public function __construct()
		{
			error_reporting(E_ALL);
			
			// these are expensive opperations this will give the server enough time
			set_time_limit(0);
			ini_set('memory_limit', '2048M');
		}
		
	}
