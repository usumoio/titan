<?php if( !defined('BASEPATH')) exit('No direct script access allowed');

	class Connect_four extends CI_Controller {
		
		//these are some global class variables
		public $game_array 		= array();
		public $board_width 	= 7;
		public $board_length 	= 7;
		public $playing_ai 		= TRUE;
		
		public function __construct()
		{
			parent::__construct();
			error_reporting(E_ALL);
			$this->load->library('greedyengine');		
			$this->load->helper('dev');
		}
		
			
		function game()
		{
			$json_game_board 				= "";
			$moves_so_far 					= 0;
			$active_players_move 			= 1;
			$winner_declared 				= NULL;
			// $winner_declared 				= new stdClass;
			// $winner_declared->score_array 	= array(0, 0);
			// $winner_declared->game_board 	= $this->game_array;

			//debug($_POST, 'post', 1);
			
			if(!empty($_POST)) 
			{
				if(array_key_exists('move', $_POST))
				{
					$move 					= (int)$_POST['move'];
					if($move <= 0 || $move > $this->board_length) 
					{
						echo "Go Fuck Yourself Andy!"; exit;
					}
					$move					= --$move;	//shift the move from input 1 ~ 7
					$json_game_board 		= json_decode($_POST['game_board']);
					$moves_so_far 			= intval($_POST['moves_so_far']); 
					$active_players_move	= intval($_POST['active_players_move']);
					
					$moves_so_far++;
							
				 	if(empty($moves_so_far)) 
				 	{
						$this->build_game_board($this->game_array);
						$json_game_board = json_encode($this->game_array);
					} else {
						$this->game_array = $this->build_board_from_json($json_game_board);
					}
					
					// this is where the move is calculated
					$json_game_board = $this->apply_move($move, $active_players_move);
					
					$winner_declared = $this->connectfour->check_for_winner_or_draw($this->game_array, $active_players_move);
						
					// if the ai is playing then the ai will move now
					if($this->playing_ai && !($winner_declared == -1 || $winner_declared === "x"))
					{
						$active_players_move 	= ($active_players_move == 1) ? 2 : 1;
						$this->game_array 		= $this->connectfour->ai_player_move($this->game_array, $active_players_move);
						$json_game_board 		= json_encode($this->game_array);
						$moves_so_far 			= $moves_so_far + 1;
						
						// TODO this cannot be how this is calculated now
						$winner_declared 		= $this->connectfour->check_for_winner_or_draw($this->game_array, $active_players_move);
					}
				}
			} else {
				$this->build_game_board($this->game_array);
				$json_game_board = json_encode($this->game_array);
			}
			
			if(!empty($winner_declared))
			{
				// if the human is moving then calculate that they won if they won
				if($active_players_move == 1)
				{
					if($winner_declared == 'x')
					{
						$winner_declared = "x";
					}
					
					if($winner_declared == -1)
					{
						$winner_declared = "d";
					}
				}
				
				if($active_players_move == 2)
				{
					if($winner_declared == 'o')
					{
						$winner_declared = "o";
					}
					
					if($winner_declared == -1)
					{
						$winner_declared = "d";
					}
				}
			}
			
			$view_data['json_board'] 			= $json_game_board;
			$view_data['board'] 				= $this->game_array;
			$view_data['moves_so_far']			= $moves_so_far;
			
			if($this->playing_ai) 
			{
				$active_players_move = 1;	
			}
			
			$view_data['active_players_move']	= $active_players_move;
			$view_data['winner_declared']		= $winner_declared;
			$this->load->view('connect_four', $view_data);
		}
		
		private function apply_move($move, $active_players_move)
		{
			$board_for_function = $this->game_array;
			$location_of_new_pieces = 0;
			
			foreach($board_for_function[$move] as $column_key => $column_space)
			{
				// check if you are at the edge of an empty row
				if(!array_key_exists(($location_of_new_pieces + 1), $board_for_function[$move]) && $column_space == '_')
				{
					$this->game_array[$move][$location_of_new_pieces] = ($active_players_move == 1) ? 'x' : 'o';
					break;
				}
				
				// check if the next place has stuff in it too
				if($column_space != '_')
				{
					// check the edge of the board to make sure that exists
					if(array_key_exists(($location_of_new_pieces - 1), $board_for_function))
					{
						$this->game_array[$move][$location_of_new_pieces - 1] = ($active_players_move == 1) ? 'x' : 'o';
						break;
					} else {
						echo "well fuck...1"; exit;
					}
				}

				$location_of_new_pieces++;
			}
			
			return json_encode($this->game_array);
		}
		
		private function build_game_board(&$game_board) 
		{
			$length = $this->board_length;
			$width = $this->board_width;	
			for($i = 0; $i < $length; $i++)
			{
				$game_board[$i] = array();
				
				for($j = 0; $j < $width; $j++)
				{
					$game_board[$i][$j] = '_';
				}
			}
		}
		
		private function build_board_from_json($json_array) 
		{
			$length = $this->board_length;
			$width = $this->board_width;
			$proxy_board = array();
			for($i = 0; $i < $length; $i++)
			{
				$proxy_board[$i] = array();
				
				for($j = 0; $j < $width; $j++)
				{
					$proxy_board[$i][$j] = $json_array[$i][$j];
				}
			}
			return $proxy_board;
		}
		
	}
	

