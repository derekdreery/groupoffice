<?php

	$the_forum = $forum->get_forum($forum_id);
	for ($i = 1;$i<4;$i++)
	{
		if ( $the_forum["custom$i"] != "" )
		{
			echo '<tr>';
			echo '<td>'.$the_forum["custom$i"].'</td>';
			$chaine = 'cf'.$i.'_body';
			echo '<td><input type="text" class="textbox" size="50" name="'.$chaine.'" value="'.$$chaine.'"</td>';
			echo '</tr>';
		}
	}
	
?>