<?php if( !defined('BASEPATH')) exit('No direct script access allowed');

	class Connect_four_model extends CI_Model
	{
			
		function __construct()
		{
		    // Call the Model constructor
		    parent::__construct();
		}
		
		
		//these are some global class variables
		public $board_width 	= 7;
		public $board_length 	= 7;
		public $playing_ai 		= TRUE;
		public $game_array 		= array();	
		public $game_board		= array();	
			
		public function apply_move($move, $active_players_move)
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
		
		
		public function build_board_from_json($json_array) 
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
		
		
		public function check_for_winner_or_draw($game_array, $active_player_move)
		{
			$present_possible_winner 	= "";
			$count_to_win 				= 0;
			$game_not_a_draw			= FALSE;
			
			for($i = 0; $i < $this->board_length; $i++)
			{
				for($j = 0; $j < $this->board_width; $j++)
				{
					// start checking for a winner
					if($game_array[$i][$j] !== "_")
					{
						$present_possible_winner = $game_array[$i][$j]; 
						
						// check for a winner horizontally
						for($x = 0; $x < 4; $x++)
						{
							if($j+$x < $this->board_width)
							{
								if($game_array[$i][$j+$x] === $present_possible_winner)
								{
									$count_to_win = $count_to_win + 1;
								}
							}
						}
						
						if($count_to_win > 3)
						{
							return $present_possible_winner;	// this player has won
						} else {
							$count_to_win = 0;
						}
						
						// check for a winner horizontally
						for($y = 0; $y < 4; $y++)
						{
							if($i+$y < $this->board_width)
							{
								if($game_array[$i+$y][$j] === $present_possible_winner)
								{
									$count_to_win = $count_to_win + 1;
								}
							}
						}
						
						if($count_to_win > 3)
						{
							return $present_possible_winner;	// this player has won
						} else {
							$count_to_win = 0;
						}
						
						// check for a winner up to down diagonal
						for($z = 0; $z < 4; $z++)
						{
							if(($i+$z < $this->board_width) && ($j+$z < $this->board_length))
							{
								if($game_array[$i+$z][$j+$z] === $present_possible_winner)
								{
									$count_to_win = $count_to_win + 1;
								}
							}
						}
						
						if($count_to_win > 3)
						{
							return $present_possible_winner;	// this player has won
						} else {
							$count_to_win = 0;
						}
						
						// check for a winner down to up diagonal
						for($w = 0; $w < 4; $w++)
						{
							if(($i+$w < $this->board_width) && ($j-$w >= 0))
							{
								if($game_array[$i+$w][$j-$w] === $present_possible_winner)
								{
									$count_to_win = $count_to_win + 1;
								}
							}
						}
						
						if($count_to_win > 3)
						{
							return $present_possible_winner;	// this player has won
						} else {
							$count_to_win = 0;
						}
					}
				}
			}

			// check for a drawed game and return accordingly
			for($i = 0; $i < $this->board_length; $i++)
			{
				for($j = 0; $j < $this->board_width; $j++)
				{
					if($game_array[$i][$j] === "_")
					{
						$game_not_a_draw = TRUE;
					}
				}
			}
			
			if(!$game_not_a_draw)
			{
				return -1;
			}
			
			return 0;
		}
		
		
		public function check_winner($active_players_move, $winner_declared){
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
			return $winner_declared;
		}
		
		
		public function build_game_board(&$game_board) 
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
		
		
		public function get_game_board() 
		{
			return $this->game_array;
		}
		
		public function get_board_width() 
		{
			return $this->board_width;
		}
		
		public function get_board_length()
		{
			return $this->board_length;
		}
	}
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		