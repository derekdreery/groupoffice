<?php
class cms_comments {

	var $query;
	var $attributes;
	var $cms_site;
	
	function cms_comments($attributes, &$cms_site)
	{
		$this->attributes=$attributes;
		$this->cms_site = &$cms_site;
		$this->query = isset($_POST['query']) ? smart_stripslashes($_POST['query']) : '';

	}
	
	function get_header()
	{
	}
	
	function has_permission()
	{
		return true;
	}
	
	
	function get_name()
	{
		global $GO_LANGUAGE;
		require($GO_LANGUAGE->get_language_file('cms'));
		return $cms_commentsform;
	}
	
	function get_html()
	{
		global $GO_LANGUAGE, $GO_SECURITY;
		
		$form = new form('comments_form');
		$form->add_html_element(new input('hidden','user_id', '', false));
		$form->add_html_element(new input('hidden', 'comments_task', 'add'));
		$form->add_html_element(new input('hidden','file_id', $this->cms_site->file_id));
		$form->add_html_element(new input('hidden','folder_id', $this->cms_site->folder_id));
		
		$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
		
	
		require($GO_LANGUAGE->get_language_file('cms'));
		
		if(isset($_POST['comments_task']))
		{			
			$comment['name'] = smart_addslashes($_POST['name']);
			$comment['comments'] = smart_addslashes($_POST['comments']);
			$comment['user_id'] = isset($GO_SECURITY->user_id) ? $GO_SECURITY->user_id : 0;
			$comment['file_id']=$this->cms_site->file_id;
			
			if($comment['name']=='' || $comment['comments']=='')
			{
				$feedback = $cms_comments_missing_field;	
			}else {								
				$this->cms_site->add_comment($comment);
			}
		}
		
		
		if(isset($feedback))
		{
			$p = new html_element('p', $feedback);
			$p->set_attribute('class', 'error');
			$form->add_html_element($p);							
		}
		
		
		//$form->add_html_element($span);
		
		if($this->cms_site->get_comments($this->cms_site->file_id))
		{
			while($this->cms_site->next_record())
			{
				$div = new html_element('div');
				$div->set_attribute('class', 'comment');
				
				$span = new html_element('span', htmlspecialchars($this->cms_site->f('name')));
				$span->set_attribute('class','comment_name');
				$div->add_html_element($span);
				
				$span = new html_element('span', date('d-m-Y G:i', gmt_to_local_time($this->cms_site->f('ctime'))));
				$span->set_attribute('class','comment_date');									
				$div->add_html_element($span);
				
				$span = new html_element('span',text_to_html($this->cms_site->f('comments')));
				$span->set_attribute('class','comment_comments');									
				$div->add_html_element($span);
				
				$form->add_html_element($div);							
			}
		}
		
		
		if($this->attributes['allow_unregistered']=='true' || $GO_SECURITY->logged_in())
		{		
			$h1 = new html_element('h1', $cms_add_comment);
			$h1->set_attribute('class', 'add_comment_title');
			$form->add_html_element($h1);
			
			$table = new table();
			$table->set_attribute('class','comments');
			
			$row = new table_row();
			$row->add_cell(new table_cell($GLOBALS['strName'].':'));
			$name = $GO_SECURITY->logged_in() ? $_SESSION['GO_SESSION']['name'] : '';
			$input = new input('text', 'name', $name);
			$input->set_attribute('style', 'width:100%;');
			$row->add_cell(new table_cell($input->get_html()));
			$table->add_row($row);
			
			$row = new table_row();
			$row->add_cell(new table_cell($GLOBALS['strComments'].':'));
			$textarea = new textarea('comments', '');
			$textarea->set_attribute('style', 'width:100%;height:80px;');
			$row->add_cell(new table_cell($textarea->get_html()));
			$table->add_row($row);
			
			
			$form->add_html_element($table);
			
			
			$input = new input('submit', 'submit', $cms_add_comment);
			$input->set_attribute('class','comments_input');
			
			$span = new html_element('span', $input->get_html());
			$span->set_attribute('class','comments_input');
			$form->add_html_element($span);
			
	
		}else {
			$form->innerHTML .= '<br />';
			$hyperlink = new hyperlink($this->cms_site->get_login_uri(), $cms_comments_login);
			//$span->set_attribute('class', 'comments_text');
			
			
			$form->add_html_element($hyperlink);
		}
		return $form->get_html();
		
	}
}
?>
