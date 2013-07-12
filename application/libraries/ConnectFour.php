<?php 

	class ConnectFour
	{
		public function __construct()
		{
			error_reporting(E_ALL);
			// these are expensive opperations this will give the server enough time
			set_time_limit(0);
			ini_set('memory_limit', '2048M');
		}
		
		public $board_width 	= 7;
		public $board_length	= 7;
		public $game_array 		= array();
		public $depth_limit 	= 5;
		
		public function ai_player_move($game_array, $active_players_move)
		{
			$this->game_array = $game_array;			
			$this->game_array = $this->calculate_ai_move($active_players_move);
			return $this->game_array;	
		}
		
		public function calculate_ai_move($active_players_move)
		{
			$move_weight_array 			= array();
			$prime_game_board 			= array();
			$prime_game_board_instance 	= NULL;
			$weight_array 				= array();
			$selected_move 				= NULL;
			
			$random_move = rand(0, 6);
					
			return $this->apply_prime_move($random_move, 2, $this->game_array);
		}
		
		
		
		// TODO apply prime move needs to also check for a winner
		public function apply_prime_move($move, $active_players_move, $board_to_use)
		{
			$location_of_new_pieces = 0;
			
			// check if the prime move to be applied is possible and if not then move elsewhere
			if(!$this->move_possible($move, $board_to_use)) {
				return FALSE;
			}
			
			foreach($board_to_use[$move] as $column_key => $column_space)
			{
				// check if you are at the edge of an empty row
				if(!array_key_exists(($location_of_new_pieces + 1), $board_to_use[$move]) && $column_space === '_')
				{
					$board_to_use[$move][$location_of_new_pieces] = ($active_players_move === 1) ? 'x' : 'o';
					break;
				}
				
				// check if the next place has stuff in it too
				if($column_space != '_')
				{
					// check the edge of the board to make sure that exists
					if(array_key_exists(($location_of_new_pieces - 1), $board_to_use))
					{
						$board_to_use[$move][$location_of_new_pieces - 1] = ($active_players_move === 1) ? 'x' : 'o';
						break;
					} else {
						echo "well fuck...3"; exit;
					}
				}

				$location_of_new_pieces++;
			}
			
			return $board_to_use;
		}
		
		public function move_possible($move, $game_board)
		{
			// check that this move is not going to fall out of the board
			if($game_board[$move][0] !== "_")
			{
				return FALSE;
			} else {
				return TRUE;
			}
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
							// $this->debug($count_to_win, "Count to win1");
							// $this->debug($present_possible_winner, "present winner", 0);
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
							// $this->debug($count_to_win, "Count to win2");
							// $this->debug($present_possible_winner, "present winner", 0);
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
							// $this->debug($count_to_win, "Count to win3");
							// $this->debug($present_possible_winner, "present winner", 0);
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
							// $this->debug($count_to_win, "Count to win4");
							// $this->debug($present_possible_winner, "present winner", 0);
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
		
		public function debug($value = NULL, $name = NULL, $exit = NULL)
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
		
	}

?>