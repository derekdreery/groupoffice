<?php
$notes_module = isset($GO_MODULES->modules['notes']) ? $GO_MODULES->modules['notes'] : false;
if ($notes_module && $notes_module['read_permission'])
{
	require_once($notes_module['class_path'].'notes.class.inc');
	$notes = new notes();

	echo 	'<table border="0" style="margin-top: 10px"><tr><td valign="top">'.
			'<img style="margin-right: 10px;" width="32" height="32" src="'.$GO_THEME->images['notes'].
			'" border="0" /></td><td>'.
			'<h2><a href="'.$notes_module['url'].'">'.
			$lang_modules['notes'].'</a></h2></td></tr><tr><td></td><td>';

	if($notes->get_new_notes() > 0)
	{
		echo '<table border="0">';
		while($notes->next_record())
		{
			echo 	'<tr><td><a href="'.$notes_module['url'].
						'note.php?note_id='.$notes->f('id').'">'.$notes->f('name').
						'</a></td></tr>';
		}
		echo '</table>';
	}else
	{
		require_once($GO_LANGUAGE->get_language_file('notes'));
		echo $no_no_notes;
	}
	echo '</td></tr></table>';

}
?>
