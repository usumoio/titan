<?php if( !defined('BASEPATH')) exit('No direct script access allowed');

	class GreedyEngine
	{
		public function __construct()
		{
			error_reporting(E_ALL);
			// these are expensive opperations this will give the server enough time
			set_time_limit(0);
			ini_set('memory_limit', '2048M');
			//$this->load->helper('dev');
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
					
			// we hardcast the active player because at this point we know it is the AI
			// here we also have to prime the move computer
			for($q = 0; $q < $this->board_length; $q++)
			{
				// MAGIC NUMBERS are the active players!!!
				$prime_game_board_instance = $this->apply_prime_move($q, 2, $this->game_array);

				$prime_game_board[] = $prime_game_board_instance;

				// check that the prime move does not win you the game for the other player
				$winner_detected = $this->check_for_winner_or_draw($prime_game_board[$q], 2);
				
				// TODO figure out what to do in the draw case
				if($winner_detected === 'o') 
				{
					return $prime_game_board[$q];
				}
				
				if($prime_game_board[$q] !== FALSE) {
					$move_weight_array[] = $this->ai_move_helper($prime_game_board[$q], 2, 0);				
				} else {
					$move_weight_array[] = FALSE;
				}
			}
			unset($q);
			
			//choose your move based on the wieghted average all your progress
			for($u = 0; $u < $this->board_length; $u++)
			{
				if($move_weight_array[$u] !== FALSE) {
					$weight_array[] = ($move_weight_array[$u][0] * 5) + ($move_weight_array[$u][1] * 2) - ($move_weight_array[$u][2] * 5); 				
				} else {
					$weight_array[] = FALSE;
				}		
			}
			
			// TODO the start of the weight contant will need to be something better
			$weight_constant = -1;
			$move_to_send = 0;		
						
			// based on the results of the move decided select the best move
			for($t = 0; $t < $this->board_length; $t++) 
			{		
				if($weight_array[$t] !== FALSE) {
					// equality here is a to avoid states here every move is equal, I think?
					if($weight_constant <= $weight_array[$t])
					{
						$weight_constant = $weight_array[$t];
						$move_to_send = $t;
					}
				} 
			}
			
			return $prime_game_board[$move_to_send];
		}
		
		public function ai_move_helper($game_board, $active_player, $depth)
		{
			// build the object that will be needed at this level of recusion
			$depth = $depth + 1;
			$score_object 								= new stdClass;
			$move_array 								= array();
			$game_boards_generated_at_this_level 		= array(); 
			$new_game_boards_generated_at_this_level 	= array();
			$return_end_state_detected					= 0;
			$score_agregate								= array(); 			
			
			if($this->depth_limit < $depth)
			{
				$score_agregate[0] = 0; 
				$score_agregate[1] = 0;
				$score_agregate[2] = 0;
				return $score_agregate; 
			}
			
			$active_player = ($active_player == 1) ? 2 : 1;
			
			// check for possible moves
			for($i=0; $i < $this->board_width; $i++)
			{	
				// calculate all of the possible recusions (all of the next moves)
				$game_boards_generated_at_this_level[$i] = $this->apply_ai_move($i, $active_player, $game_board);
				
				// this is the recusive level
				$score_agregate = $this->ai_move_helper($game_boards_generated_at_this_level[$i]->game_board, $active_player, $depth);				
			}
			
			unset($i);
			
			
			// check to see if there are more moves of if it is time to return
			foreach($game_boards_generated_at_this_level as $game_state)
			{
				//compute the agragate of the scores only for player two (AI)
				if($active_player === 2)
				{
					//THE WAY SCORES ARE AGREGATED HERE IS WRONG
					$score_agregate[0] = $score_agregate[0] + $game_state->score_array[0]; 
					$score_agregate[1] = $score_agregate[1] + $game_state->score_array[1];				
					$score_agregate[2] = $score_agregate[2] + $game_state->score_array[2];				
				} else if($active_player === 1) {
					$score_agregate[0] = $score_agregate[0] + $game_state->score_array[2]; 
					$score_agregate[1] = $score_agregate[1] + $game_state->score_array[1];
					$score_agregate[2] = $score_agregate[2] + $game_state->score_array[0]; 
				}
			}
			return $score_agregate;
		}
		
		public function apply_ai_move($move, $active_players_move, $board_to_use)
		{
			$board_for_function		= array();
			$location_of_new_pieces = 0;
			$return_object 			= new stdClass;
			
			// this makes sure that this function is being called with the right board
			if(!empty($board_to_use))
			{
				$board_for_function = $board_to_use;
			} else {
				$board_for_function = $this->game_array;
			}
			
			// check if there is no point in applying this move, because a player has already won
			$test_for_complete = $this->check_for_winner_or_draw($board_for_function, $active_players_move);

			if(($test_for_complete == -1) || ($test_for_complete === 'x') || ($test_for_complete === 'o'))
			{
				$return_object->game_board = $board_to_use;
				if($active_players_move === 1)
				{
					if($test_for_complete === -1) 
					{
						$return_object->score_array = array(0, 1, 0);
					} else if($test_for_complete === 'x'){
						$dump_test = 1;						
						$return_object->score_array = array(0, 0, 1);
					} else {
						$return_object->score_array = array(0, 0, 0);						
					}
				} else if($active_players_move === 2) {
					if($test_for_complete === -1) 
					{
						$return_object->score_array = array(0, 1, 0);
					} else if($test_for_complete === 'o'){
						$return_object->score_array = array(1, 0, 0);
					} else {						
						$return_object->score_array = array(0, 0, 0);						
					}
				} else {
					$this->debug('fuck', '', 1);
				}
								
				return $return_object;
			}
			
			// check that this move is possible
			if(!$this->move_possible($move, $board_for_function))
			{
				$return_object->game_board 		= $board_for_function;
				$return_object->score_array 	= array(0, 0, 0);
				return $return_object;
			}
			
			// this part of the function applies a valid move
			foreach($board_for_function[$move] as $column_key => $column_space)
			{
				// check if you are at the edge of an empty row
				if(!array_key_exists(($location_of_new_pieces + 1), $board_for_function[$move]) && $column_space == '_')
				{
					$board_for_function[$move][$location_of_new_pieces] = ($active_players_move == 1) ? 'x' : 'o';
					break;
				}
				
				// check if the next place has stuff in it too
				if($column_space != '_')
				{
					// check the edge of the board to make sure that exists
					if(array_key_exists(($location_of_new_pieces - 1), $board_for_function))
					{
						$board_for_function[$move][$location_of_new_pieces - 1] = ($active_players_move == 1) ? 'x' : 'o';
						break;
					} else {
						echo "well fuck...2"; exit;
					}
				}

				$location_of_new_pieces++;
			}
			
			$return_object->game_board = $board_for_function;
			
			// now check if this state is a win loss or draw
			$test_for_complete = $this->check_for_winner_or_draw($board_for_function, $active_players_move);
			
			// this is a draw
			if($test_for_complete === -1)
			{
				$return_object->score_array = array(0, 1, 0);
			} else if($test_for_complete === 'o') {
				$return_object->score_array = array(1, 0, 0);
			} else if($test_for_complete === 'x') {
				$return_object->score_array = array(0, 0, 1);
			} else {
				$return_object->score_array = array(0, 0, 0);
			}
			
			return $return_object;
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
			
		
	}

?>