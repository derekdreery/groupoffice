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
		global $GO_LANGUAGE, $GO_SECURITY, $GO_CONFIG;
		
		
		$antispams[]=array('question'=>'1 + 3 = ', 'answer'=>'4');
		$antispams[]=array('question'=>'1 + 2 = ', 'answer'=>'3');
		$antispams[]=array('question'=>'4 / 2 = ', 'answer'=>'2');
		$antispams[]=array('question'=>'5 + 3 = ', 'answer'=>'8');
		$antispams[]=array('question'=>'2 x 2 = ', 'answer'=>'4');
			
		
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
			
			if($anitspams[$_POST['antispam_key']] == $_POST['antispam_answer'])
			{
				$feedback = $cms_antispam_fail;	
			}elseif($comment['name']=='' || $comment['comments']=='')
			{
				$feedback = $cms_comments_missing_field;	
			}else {								
				$this->cms_site->add_comment($comment);
				
				if(!empty($this->attributes['email']))
				{
					$file = $this->cms_site->get_file($this->cms_site->file_id);
					
					$subject = sprintf($cms_new_comment_subject, strip_extension($file['name']));
					
					
					$body = $GLOBALS['strName'].': '.stripslashes($comment['name'])."\n\n".
						stripslashes($comment['comments'])."\n\n";
						
					if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']!='off')			
					{
						$body .= 'https://';
					}else {
						$body .= 'http://';
					}
					$body .= $_SERVER['HTTP_HOST'].$this->cms_site->create_url($file['folder_id'], $file['id']);
					
					sendmail($this->attributes['email'],$GO_CONFIG->webmaster_email,$GO_CONFIG->title,$subject,$body);
				}
				
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
			
			$cell = new table_cell($GLOBALS['strName'].':');
			$cell->set_attribute('style','white-space:nowrap');
			$row->add_cell($cell);
			
			$name = $GO_SECURITY->logged_in() ? $_SESSION['GO_SESSION']['name'] : '';
			$input = new input('text', 'name', $name);
			$input->set_attribute('style', 'width:99%;');
			$cell=new table_cell($input->get_html());
			$cell->set_attribute('style','width:100%');
			$row->add_cell($cell);
			$table->add_row($row);
			
			$row = new table_row();
	
			$cell = new table_cell($GLOBALS['strComments'].':');
			$cell->set_attribute('style','white-space:nowrap;vertical-align:top');
			$row->add_cell($cell);
			
			$textarea = new textarea('comments', '');
			$textarea->set_attribute('style', 'width:99%;height:80px;');
			$cell=new table_cell($textarea->get_html());
			$cell->set_attribute('style','width:100%');
			$row->add_cell($cell);
			$table->add_row($row);
			
			
			
			
			
			$row = new table_row();
	
			$cell = new table_cell($cms_antispam_text);
			$cell->set_attribute('colspan','2');
	
			$row->add_cell($cell);
			$table->add_row($row);
			
			
			
			
			$row = new table_row();
			
			
			
			$antispam_key = array_rand($antispams);
			
			$cell = new table_cell($antispam_questions[$antispam_key]['question'].':');
			$cell->set_attribute('style','white-space:nowrap');
			$row->add_cell($cell);
			
			$form->add_html_element(new input('hidden','antispam_key',$antispam_key,false));
			
			
			$input = new input('text', 'antispam_answer', '');
			$input->set_attribute('style', 'width:99%;');
			$cell=new table_cell($input->get_html());
			$cell->set_attribute('style','width:100%');
			$row->add_cell($cell);
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
