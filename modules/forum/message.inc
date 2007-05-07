	

<table class="msg_view">
<tr>
	<td class="msg_title"><?php echo $msg_obj['title']; ?></td>
</tr>
<tr>
	<td class="msg_id">
		<?php
			echo $frm_msg_no.' '.$msg_obj['id'].' '.$frm_posted_by.' '.$user['first_name'].' '.$user['last_name'];
			if ( $msg_obj['parent_id'] > 0 )
			{
				$f1 = new forum();
				$tobj = $f1->get_message($msg_obj['parent_id']);
				echo ' '.$frm_in_answer_to.' <a href="javascript:change_msg(\''.$tobj['id'].'\')">"'.$tobj['title'].'"</a>';
			}
			
		?>
	</td>
</tr>
<tr>
	<td class="msg_message"><?php echo $msg_obj['message']; ?></td>
</tr>
<?php
	$files = $forum->get_files_from_message($msg_obj['id']);
	$cnt = count($files);
?>
<tr>
<td>
			<?php
				if ( $cnt > 0)
					{
						for ($i=0;$i<$cnt;$i++)
						{
							$mid = $msg_obj['id']; $fid = $msg_obj['forum_id'];
							$rep = $GO_CONFIG->file_storage_path."forum/$fid/$mid/";
			
							$ext = get_extension($files[$i]['name']);
							$image = get_filetype_image($ext);
							echo '<a href ="download.php?path='.$rep.$files[$i]['name'].'"><img title="'.$files[$i]['name'].'" src="'.$image.'" border="0" />'.$files[$i]['name'].'</a> ';
						}
					}
				?>
</td>	
</tr>
</table>
<?php if ($tree != '') echo '<span class="msg_responses">'.$frm_responses.'</td></tr>';
