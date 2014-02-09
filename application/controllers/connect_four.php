<?php if( !defined('BASEPATH')) exit('No direct script access allowed');

	class Connect_four extends CI_Controller 
	{
		public $playing_ai						= TRUE;

		public function __construct()
		{
			parent::__construct();
			error_reporting(E_ALL);
			$this->load->library('greedyengine');		
			$this->load->helper('dev');
		}
		
			
		function game()
		{
			// load our model here
			$this->load->model('Connect_four_model', 'connect_four');
			$game_array = $this->connect_four->get_game_board();
			$json_game_board 				= "";
			$moves_so_far 					= 0;
			$active_players_move 			= 1;
			$winner_declared 				= NULL;
			$board_length					= $this->connect_four->get_board_length();
			$board_width					= $this->connect_four->get_board_width();
			//debug($_POST, 'post', 1);
			
			if(!empty($_POST)) 
			{
				if(array_key_exists('move', $_POST))
				{
					$move 					= (int)$_POST['move'];
					
					// this is a safty check
					if($move <= 0 || $move > $board_length) 
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
						$this->build_game_board($game_array);
						$json_game_board = json_encode($game_array);
					} else {
						$game_array = $this->connect_four->build_board_from_json($json_game_board);
					}
					
					// this is where the move is calculated
					$json_game_board = $this->connect_four->apply_move($move, $active_players_move);
					
					$winner_declared = $this->connect_four->check_for_winner_or_draw($game_array, $active_players_move);
						
					// if the ai is playing then the ai will move now
					if($this->playing_ai && !($winner_declared == -1 || $winner_declared === "x"))
					{
						$active_players_move 	= ($active_players_move == 1) ? 2 : 1;
						$game_array 		= $this->connectfour->ai_player_move($game_array, $active_players_move);
						$json_game_board 		= json_encode($game_array);
						$moves_so_far 			= $moves_so_far + 1;
						
						// TODO this cannot be how this is calculated now
						$winner_declared 		= $this->connectfour->check_for_winner_or_draw($game_array, $active_players_move);
					}
				}
			} else {
				$this->connect_four->build_game_board($game_array);
				$json_game_board = json_encode($game_array);
			}
			
			if(!empty($winner_declared))
			{
				$winner_declared = $this->connect_four->check_winner($active_players_move, $winner_declared);
			}
			
			$view_data['json_board'] 			= $json_game_board;
			$view_data['board'] 				= $game_array;
			$view_data['moves_so_far']			= $moves_so_far;
			
			if($this->playing_ai) 
			{
				$active_players_move = 1;	
			}
			
			$view_data['active_players_move']	= $active_players_move;
			$view_data['winner_declared']		= $winner_declared;
			$this->load->view('connect_four', $view_data);
		}

		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	

