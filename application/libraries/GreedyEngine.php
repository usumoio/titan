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
		public $depth_limit 	= 3;
		
		public function ai_player_move($game_array)
		{
			$this->game_array = $game_array;			
			$this->game_array = $this->calculate_ai_move();
			return $this->game_array;	
		}
		
		
		// this returns the game board after the ai players move
		public function calculate_ai_move() 
		{
			$win 			= $this->win($this->game_array);
			$winning_move 	= array();
			$move_on_loss	= NULL;
			
			if($win) 
			{
				return $win;
			}
		
			$move_on_loss = $this->move_on_loss($this->game_array);
		
			$block_loss = $this->block_loss($this->game_array);
		
			for($i = 0; $i < $this->board_width; $i++) 
			{	
				if(!array_key_exists($i, $block_loss))
				{
					$move_ahead = $this->move_ahead($this->game_array);	
					
					foreach($move_ahead as $key => $check_win)
					{
						if($check_win)
						{
							list($winning_move, $message) = $this->apply_move($key, 2, $this->game_array);
							return $winning_move; 
						}
					}				
				}
			}
		
			if (is_null($move_on_loss)) 
			{
				while(TRUE)
				{
					$move  = rand(0, 6);
					
					//TODO this does not stop illegal moves
					if(!array_key_exists($move, $block_loss)) 
					{
						list($game_board, $error_message) = $this->apply_move($move, 2, $this->game_array);
						return $game_board;	
					}
				}
			} else {
				list($game_board, $error_message) = $this->apply_move($move_on_loss, 2, $this->game_array);
				return $game_board;	
			}
		}
		
		
		public function move_on_loss($game_board)
		{
			$error_message 	= '';
			$active_player 	= 1;
			
			for($i = 0; $i < $this->board_width; $i++)
			{
				list($board, $error_message) = $this->apply_move($i, $active_player, $game_board);
				
				if(!$error_message)
				{	
					$win_value = $this->check_for_winner_or_draw($board);				
				} else {
					return FALSE;
				}
			
				if(($win_value == 'x') && (!$error_message)) 
				{
					return $i;	
				}
			}
			
			return NULL;
		}
		
		
		public function win($game_board) 
		{
			
			$error_message 	= '';
			$active_player 	= 2;
			
			for($i = 0; $i < $this->board_width; $i++)
			{
				list($board, $error_message) = $this->apply_move($i, $active_player, $game_board);
				
				if(!$error_message)
				{	
					$win_value = $this->check_for_winner_or_draw($board);				
				} else {
					return FALSE;
				}
			
				if(($win_value == 'o') && (!$error_message)) 
				{
					return $board;	
				}
			}
			
			return FALSE;
		}
		
		
		public function block_loss($game_board) 
		{
			$active_player			= 2;
			$board					= array();
			$board_human			= array();
			$error_message			= ""; 
			$error_message_human	= "";
			$must_not_move_list		= array();
			$loss					= FALSE;
			$win_value 				= "";
			
			for($i = 0; $i < $this->board_width; $i++)
			{
				list($board, $error_message) = $this->apply_move($i, $active_player, $game_board);
				
				// switch the active player
				$active_player = ($active_player == 2) ? 1 : 2;
				
				if($this->board_full($board, $active_player))
				{
					return $game_board;
				}
				
				//make a counter move and see if anywhere beats the AI
				for($j = 0; $j < $this->board_width; $j++)
				{
					if(!$error_message) 
					{
						list($board_human, $error_message_human) = $this->apply_move($j, $active_player, $board);					
					
						if(!$error_message_human)
						{
							$win_value = $this->check_for_winner_or_draw($board_human);					
						}
				
						if(($win_value == 'x' ) && (!$error_message_human)) 
						{
							$must_not_move_list[$i] = TRUE; 
						} 
					} 
				}
			}
			
			return $must_not_move_list;
		}
		
		
		public function move_ahead($game_board) 
		{
			$active_player			= 2;
			$board					= array();
			$board_ai_2				= array();
			$board_human			= array();
			$error_message			= ""; 
			$error_message_ai_2		= ""; 
			$error_message_human	= "";
			$must_move_list			= array();
			$loss					= FALSE;
			$win_value				= "";
			
			for($i = 0; $i < $this->board_width; $i++)
			{
				list($board, $error_message) = $this->apply_move($i, $active_player, $game_board);
				
				// switch the active player
				$active_player = ($active_player == 2) ? 1 : 2;
				
				if($this->board_full($game_board, $active_player))
				{
					return $game_board;
				}
				
				//make a counter move and see if anywhere beats the AI
				for($j = 0; $j < $this->board_width; $j++)
				{
					if(!$error_message) 
					{
						list($board_human, $error_message_human) = $this->apply_move($j, $active_player, $board);					
					}
					
					// switch the active player
					$active_player = ($active_player == 2) ? 1 : 2;
					
					//make a counter move and see if anywhere beats the AI
					for($k = 0; $k < $this->board_width; $k++)
					{
						if(!$error_message) 
						{
							list($board_ai_2, $error_message_ai_2) = $this->apply_move($k, $active_player, $board_human);					
						
							if(!$error_message_ai_2)
							{
								$win_value = $this->check_for_winner_or_draw($board_ai_2);						
							}
							
							if(($win_value == 'o' ) && (!$error_message_ai_2)) 
							{
								$must_move_list[$i] = TRUE; 
							} 
						}
					}	
				}
			}
			return $must_move_list;
		}


		public function apply_move($move, $active_players_move, $game_array) 
		{
			$board_for_function 		= $game_array;
			$starting_game_board 		= $game_array;
			$location_of_new_pieces 	= 0;
			
			foreach($board_for_function[$move] as $column_key => $column_space)
			{
				// check if you are at the edge of an empty row
				if(!array_key_exists(($location_of_new_pieces + 1), $board_for_function[$move]) && $column_space == '_')
				{
					$game_array[$move][$location_of_new_pieces] = ($active_players_move == 1) ? 'x' : 'o';
					break;
				}
				
				// check if the next place has stuff in it too
				if($column_space != '_')
				{
					// check the edge of the board to make sure that exists
					if(array_key_exists(($location_of_new_pieces - 1), $board_for_function))
					{
						$game_array[$move][$location_of_new_pieces - 1] = ($active_players_move == 1) ? 'x' : 'o';
						break;
					} else {
						// this player made an illgal move off the board
						return array($starting_game_board, "That is not a legal move");
					}
				}

				$location_of_new_pieces++;
			}
			// return board with no errors
			return array($game_array, "");
		}


		// return x if x wins o if o wins and draw for a draw none for no winner
		public function check_for_winner_or_draw($game_array)
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
				return 'draw';
			}
			
			return 'none';
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
			
			
		// check if the board is full
		public function board_full($game_board, $active_player)
		{
			$error_message = '';
			$spot_count = 0;
			
			for($i = 0; $i < $this->board_width; $i++)
			{
				list($board, $error_message) = $this->apply_move($i, $active_player, $game_board);
				
				if($error_message) 
				{
					$spot_count = $spot_count + 1;	
				}
			}
			
			// this means that the board is full
			if($spot_count == 7) 
			{
				return TRUE;	
			}
			
			return FALSE;
		}
	}

?>