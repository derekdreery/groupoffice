<?php


$form->add_html_element(new input('hidden','move_up_id'));
$form->add_html_element(new input('hidden','move_dn_id'));
$form->add_html_element(new input('hidden','move_up_pr'));
$form->add_html_element(new input('hidden','move_dn_pr'));



if (isset($_REQUEST['move_up_id']) && $_REQUEST['move_dn_id'] != 0)
{
	$email->move_up($_REQUEST['move_up_id'], $_REQUEST['move_dn_id'], $_REQUEST['move_up_pr'], $_REQUEST['move_dn_pr']);
}

if ($email->get_subscribed($account_id) > 0)
{
	$datatable = new datatable('filters_table');
	if($datatable->task == 'delete')
	{
		foreach($datatable->selected as $filter_id)
		{
			$email->delete_filter($filter_id);
		}
	}
	
	$menu = new button_menu();
	$menu->add_button('add', $cmdAdd, 'filter.php?id='.$account_id.'&return_to='.urlencode($link_back));
	$menu->add_button('delete_big', $cmdDelete,$datatable->get_delete_handler());
	$form->add_html_element($menu);

	$count = $email->get_filters($account_id);
	$em = new email();

	if ($count>0)
	{
		$datatable->add_column(new table_heading($ml_field));
		$datatable->add_column(new table_heading($ml_contains));
		$datatable->add_column(new table_heading($ml_folder));
		
		$th = new table_heading('&nbsp;');
		$th->set_attribute('colspan','3');
		$datatable->add_column($th);
		

		$last_id  = 0;
		$last_pr = 0;

		while($email->next_record())
		{
			switch($email->f("field"))
			{
				case "sender":
					$field = $ml_sender;
				break;

				case "subject":
					$field = $ml_subject;
				break;

				case "to";
					$field = $ml_reciever;
				break;

				default:
					$field = $ml_cc_reciever;
				break;
			}

			$folder = $em->get_folder($account_id, $email->f("folder"));
			
			$row = new table_row($email->f("id"));
			$row->set_attribute('ondblclick', 'javascript:document.location=\'filter.php?id='.$account_id.'&filter_id='.$email->f("id").
				'&return_to='.urlencode($link_back).'\'');
			$row->add_cell(new table_cell($field));
			$row->add_cell(new table_cell($email->f("keyword")));
			$row->add_cell(new table_cell(str_replace('INBOX'.$folder["delimiter"], '', $folder["name"])));
			
			$img = new image('up');
			$img->set_attribute('style','border:0px;');
			
			$link = new hyperlink('javascript:move_up('.
					$email->f("id").','.$last_id.','.$last_pr.','.
					$email->f("priority").');	',
					$img->get_html(),
					htmlspecialchars($ml_move_up));
			$row->add_cell(new table_cell($link->get_html()));
		
			$last_id = $email->f("id");
			$last_pr = $email->f("priority");
			
			$datatable->add_row($row);
		}
		$tabstrip->add_html_element($datatable);
	}else
	{		
		$tabstrip->add_html_element(new html_element('p',$ml_no_filters));
	}
}else
{
	$tabstrip->add_html_element(new html_element('p',$ml_no_folders));
}

$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='".$return_to."'"));
?>
<script type="text/javascript">
function move_up(move_up_id, move_dn_id, move_up_pr,move_dn_pr)
{
	document.forms[0].move_up_id.value=move_up_id;
	document.forms[0].move_dn_id.value=move_dn_id;
	document.forms[0].move_up_pr.value=move_up_pr;
	document.forms[0].move_dn_pr.value=move_dn_pr;
	
	document.forms[0].task.value='move_up';
	document.forms[0].submit();
}

function delete_filter(delete_filter, text)
{
	if(confirm(text))
	{
		document.forms[0].delete_filter.value=delete_filter;	
		document.forms[0].task.value='move_up';
		document.forms[0].submit();
	}
}
</script>
