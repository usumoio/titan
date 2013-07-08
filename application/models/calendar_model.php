<?php if( !defined('BASEPATH')) exit('No direct script access allowed');

	class Calendar_model extends CI_Model
	{
		function __construct()
		{
			parent::__construct();
			$this->load->database();
		}
		
		//used on an origin parse
		function get_text_data()
		{
			$result = $this->db->query('SELECT * FROM `text_data` WHERE 1');
			return $result->result();
		}
		
		//create a new row for a calendar event
		function insert_calendar($update_data)
		{
			$this->db->insert('calendar_data', $update_data);
		}
		
		// get all of the calendar events
		function get_calendar_data()
		{
			$result = $this->db->query('SELECT * FROM `calendar_data` WHERE 1');
			return $result->result();
		}
		
		// get an event by its id
		function get_single_event($id)
		{
			$result = $this->db->query('SELECT * FROM `calendar_data` WHERE id = ?', array($id));
			return $result->result();   
		}
	}

