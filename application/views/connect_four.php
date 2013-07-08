
<div id="centering" style="text-align:center;" >
<h2>This is the game board:</h2>
<?php 
	//var_dump($winner_declared);

	if($winner_declared === "x") 
	{
		echo "<h1>PLAYER X HAS WON!</h1>";
	} else if ($winner_declared === "o") {
		echo "<h1>PLAYER O HAS WON!</h1>";		
	} else if ($winner_declared === "d") {
		echo "<h1>YOU HAVE DRAWN WITH THE MACHINE</h1>";
	}
?>
<h5>Please input your move from 1 ~ 7.</h5>

<?php 
	$column_length = count($board);
	$row_length = count($board[0]);

	for($y = 1; $y <= $column_length; $y++)
	{
		echo "&nbsp;";
		echo "&nbsp;";
		echo "&nbsp;";
		echo $y;
		echo "&nbsp;";
		echo "&nbsp;";
		echo "&nbsp;";
	}
	echo "<br />";
	for($i = 0; $i < $column_length; $i++)
	{
		echo "|";
		
		for($j = 0; $j < $row_length; $j++)
		{
			echo "&nbsp;";
			echo "&nbsp;";
			echo "&nbsp;";
			echo $board[$j][$i];
			echo "&nbsp;";
			echo "&nbsp;";
			echo "&nbsp;";
		}
		
		echo "|";
		echo "<br />";
	}

	for($z = 1; $z <= $column_length; $z++)
	{
		echo "&nbsp;";
		echo "&nbsp;";
		echo "&nbsp;";
		echo $z;
		echo "&nbsp;";
		echo "&nbsp;";
		echo "&nbsp;";
	}
?>
<br /><br />
<form name="connect_four" action="connect_four" method="post">
	<select name="move">
		<option value="1">1</option>
		<option value="2">2</option>
		<option value="3">3</option>
		<option value="4">4</option>
		<option value="5">5</option>
		<option value="6">6</option>
		<option value="7">7</option>
	</select>
	<input type="hidden" name="game_board" value="<?php echo htmlspecialchars($json_board) ?>" />
	<input type="hidden" name="moves_so_far" value="<?php echo $moves_so_far ?>" />
	<input type="hidden" name="active_players_move" value="<?php echo $active_players_move ?>" />
	<input type="submit" value="submit" />
</form>

<?php // echo $moves_so_far ?>

<script> //alert('dang'); </script>
</div>











