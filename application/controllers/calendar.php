<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Calendar extends CI_Controller
	{
		
		function __construct()
		{
			parent::__construct();
			
			// load our models
			$this->load->model('calendar_model', 'calendar');
			$this->load->helper('url');
		}	
		
		// run the parser to create new events
		function origin_parse()
		{
			$text_data = $this->calendar->get_text_data();
			
			// loop through each text row and parse it into feilds
			foreach($text_data as $single_column)
			{
				$location = strpos($single_column->text_column, '"');
				$single_column_part = substr($single_column->text_column, $location+1);
				$location_2 = strpos($single_column_part, '"');
				$base_info = substr($single_column->text_column, $location+1, ($location_2 - ($location)));
				$single_column->text_column = substr($single_column->text_column, $location_2+2);
						
				$location = strpos($single_column->text_column, ',');
				$single_column_part = substr($single_column->text_column, $location+1);
				$location_2 = strpos($single_column_part, ',');		
				$code = substr($single_column->text_column, $location+1, ($location_2 - ($location)));
				$single_column->text_column = substr($single_column->text_column, $location_2+1);
				
				$location = strpos($single_column->text_column, ',');
				$single_column_part = substr($single_column->text_column, $location+1);
				$location_2 = strpos($single_column_part, ',');
				$city = substr($single_column->text_column, $location+1, $location_2 - ($location));
				$single_column->text_column = substr($single_column->text_column, $location_2+1);
				
				$location = strpos($single_column->text_column, ',');
				$single_column_part = substr($single_column->text_column, $location+1);
				$location_2 = strpos($single_column_part, ',');
				$state_code = substr($single_column->text_column, $location+1, $location_2 - ($location));
				$single_column->text_column = substr($single_column->text_column, $location_2+1);

				if(strpos($single_column->text_column, ',y,') !== FALSE)
				{
					$on_site = 1;
					$single_column->text_column = substr($single_column->text_column, 4);
					
				} else if(strpos($single_column->text_column, ',n') !== FALSE ) {
					
					$on_site = 0;
					$single_column->text_column = substr($single_column->text_column, 3);
				} else {
					$on_site = 1;
				}
				
				$location = strpos($single_column->text_column, '"');
				$single_column_part = substr($single_column->text_column, $location+1);
				$location_2 = strpos($single_column_part, '"');
				$address = substr($single_column->text_column, $location+1, $location_2 - ($location));
				$single_column->text_column = substr($single_column->text_column, $location_2+3);
				
				$location_2 = strpos($single_column->text_column, ',"');
				$phone_number = substr($single_column->text_column, 0, $location_2);		
				$single_column->text_column = substr($single_column->text_column, $location_2);
				
				$location = strpos($single_column->text_column, ',"');
				$single_column_part = substr($single_column->text_column, $location+2);
				$location_2 = strpos($single_column_part, '"');
				$proper_data = substr($single_column->text_column, $location+2, $location_2 - ($location));
				$single_column->text_column = substr($single_column->text_column, $location_2+3);
								
				$location = strpos($single_column->text_column, ',');
				$single_column_part = substr($single_column->text_column, $location+1);
				$location_2 = strpos($single_column_part, ',');
				$start_time = substr($single_column->text_column, $location+1, $location_2 - ($location));
				$single_column->text_column = substr($single_column->text_column, $location_2+1);

				$location = strpos($single_column->text_column, ',');
				$single_column_part = substr($single_column->text_column, $location+1);
				$location_2 = strpos($single_column_part, ',');
				$end_time = substr($single_column->text_column, $location+1, $location_2 - ($location));
				$single_column->text_column = substr($single_column->text_column, $location_2+1);
				
				$location = strpos($single_column->text_column, ',');
				$single_column_part = substr($single_column->text_column, $location+1);
				$location_2 = strpos($single_column_part, ',');
				$course_code = substr($single_column->text_column, $location+1, $location_2 - ($location));
				$single_column->text_column = substr($single_column->text_column, $location_2+1);
				
				$location = strpos($single_column->text_column, ',');
				$single_column_part = substr($single_column->text_column, $location+1);
				$location_2 = strpos($single_column_part, ',');
				$instructor = substr($single_column->text_column, $location+1, $location_2 - ($location));
				$single_column->text_column = substr($single_column->text_column, $location_2+1);
				
				$location = strpos($single_column->text_column, ',');
				$single_column_part = substr($single_column->text_column, $location+1);
				$location_2 = strpos($single_column_part, ',');				
				$course_cost = substr($single_column->text_column, $location+1, $location_2 - ($location));
				$single_column->text_column = substr($single_column->text_column, $location_2+1);
				
				$location = strpos($single_column->text_column, ',');
				$single_column_part = substr($single_column->text_column, $location+1);
				$location_2 = strpos($single_column_part, ',');
				$random_zero = substr($single_column->text_column, $location+1, $location_2 - ($location));
				$single_column->text_column = substr($single_column->text_column, $location_2+1);
							
				$description = substr($single_column->text_column, 2, (strlen($single_column->text_column) - 2));
	
				// setup the structure of a single new event parsed out as columns that have meaning
				$constructed_cal_row = array(
					'base_info' 		=> $base_info,
					'code'				=> $code,
					'city' 				=> $city,
					'state_code'		=> $state_code,
					'on_site'			=> $on_site,
					'address'			=> $address,
					'phone_number'		=> $phone_number,
					'proper_data'		=> $proper_data,
					'start_time'		=> $start_time,
					'end_time'			=> $end_time,
					'course_code'		=> $course_code,
					'instructor'		=> $instructor,
					'course_cost'		=> $course_cost,
					'random_zero'		=> $random_zero,
					'decsription'		=> $description
				);
				
				// add that row to something that we can use
				$this->calendar->insert_calendar($constructed_cal_row);
				
			}
			
			$this->test('Done!');
		}
		
		// display contents of page
		function index()
		{
			// get every event record
			$all_events = $this->calendar->get_calendar_data();
			
			$veiw_data['all_events'] = $all_events;
			
			// load the view and send the data
			$this->load->view('index', $veiw_data);
		}
		
		// display the page for a single event
		// use the event id to grab all event data
		function single_event_view($event_id)
		{
			// call the calendar model
			$event_data = $this->calendar->get_single_event($event_id);
			
			$veiw_data['event'] = $event_data[0];
			
			//load the view for the single event with data
			$this->load->view('single_event', $veiw_data);
		}
		
		// this just made testing easier as I worked
		function test($the_data)
		{
			echo '<pre>';
			var_dump($the_data);
			echo '<pre/>';
			exit;
		}
		
	}
