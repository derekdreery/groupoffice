<?php
load_control('datatable');
class cms_users {

	var $query;
	var $attributes;
	var $cms_site;
	
	function cms_users($attributes, &$cms_site)
	{
		$this->attributes=$attributes;
		$this->cms_site = &$cms_site;
		$this->query = isset($_POST['query']) ? smart_stripslashes($_POST['query']) : '';

	}
	
	function has_permission()
	{
		return true;
	}
	
	function get_header()
	{
	}
	
	
	function get_name()
	{
		global $GO_LANGUAGE;
		require($GO_LANGUAGE->get_language_file('cms'));
		return $cms_user_search;
	}
	
	
	function get_html()
	{
		global $GO_USERS;
		
		$form = new form('users_form');
		$form->add_html_element(new input('hidden','user_id', '', false));
		$form->add_html_element(new input('hidden','task', 'search'));
		$form->add_html_element(new input('hidden','file_id', $this->cms_site->file_id));
		$form->add_html_element(new input('hidden','folder_id', $this->cms_site->folder_id));
		
		$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
		$user_id = isset($_REQUEST['user_id']) ? smart_addslashes($_REQUEST['user_id']) : '0';

		if($user_id>0)
		{
			$form->add_html_element(new input('hidden', 'query', $this->query));
			
			$user = $GO_USERS->get_user($user_id);			
			
			$table = new table();
			$table->set_attribute('style','margin-top:10px;');
			
			$row = new table_row();
			$row->add_cell(new table_cell($GLOBALS['strName'].':'));
			$row->add_cell(new table_cell(htmlspecialchars(format_name($user['last_name'], $user['first_name'], $user['middle_name'], 'first_name'))));
			$table->add_row($row);
			
			$row = new table_row();
			$row->add_cell(new table_cell($GLOBALS['strCompany'].':'));
			$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($user['company']))));
			$table->add_row($row);

			$row = new table_row();
			$row->add_cell(new table_cell($GLOBALS['strDepartment'].':'));
			$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($user['department']))));
			$table->add_row($row);

			$row = new table_row();
			$row->add_cell(new table_cell($GLOBALS['strFunction'].':'));
			$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($user['function']))));
			$table->add_row($row);

			$row = new table_row();
			$cell = new table_cell('&nbsp;');
			$cell->set_attribute('colspan','2');
			$row->add_cell($cell);
			$table->add_row($row);

			$row = new table_row();
			$row->add_cell(new table_cell($GLOBALS['strAddress'].':'));
			$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($user['work_address']))));
			$table->add_row($row);

			$row = new table_row();
			$row->add_cell(new table_cell($GLOBALS['strZip'].':'));
			$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($user['work_zip']))));
			$table->add_row($row);

			$row = new table_row();
			$row->add_cell(new table_cell($GLOBALS['strCity'].':'));
			$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($user['work_city']))));
			$table->add_row($row);

			$row = new table_row();
			$row->add_cell(new table_cell($GLOBALS['strState'].':'));
			$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($user['work_state']))));
			$table->add_row($row);

			$row = new table_row();
			$row->add_cell(new table_cell($GLOBALS['strCountry'].':'));

			$go_users = new $GLOBALS['go_users_class'];
						
			$country_text='';		
			if($country = $go_users->get_country($user['work_country_id']))
			{
				$country_text=$country['name'];
			}		
			$row->add_cell(new table_cell($country_text));
			$table->add_row($row);

			

			$row = new table_row();
			$cell = new table_cell('&nbsp;');
			$cell->set_attribute('colspan','2');
			$row->add_cell($cell);
			$table->add_row($row);

			$row = new table_row();
			$row->add_cell(new table_cell($GLOBALS['strPhone'].':'));
			$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($user['work_phone']))));
			$table->add_row($row);

			$row = new table_row();
			$row->add_cell(new table_cell($GLOBALS['strFax'].':'));
			$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($user['work_fax']))));
			$table->add_row($row);
			
			$row = new table_row();
			$row->add_cell(new table_cell($GLOBALS['strEmail'].':'));
			$row->add_cell(new table_cell('<a href="mailto:'.$user['email'].'">'.$user['email'].'</a>'));
			$table->add_row($row);

			$row = new table_row();
			$row->add_cell(new table_cell($GLOBALS['strHomepage'].':'));
			$link = new hyperlink($user['homepage'], $user['homepage']);
			$row->add_cell(new table_cell($link->get_html()));
			$table->add_row($row);
			
			$form->add_html_element($table);
			
			
			$button = new input('submit','button', $GLOBALS['cmdBack'], 'javascript:document.users_form.submit();');
			$button->set_attribute('style','margin-top:10px;');
			$form->add_html_element($button);
			
			
		}else
		{		
			$input = new input('text','query', $this->query);
			$input->set_attribute('style', 'width:200px');
			
			$form->add_html_element($input);
			
			$datatable = new datatable('cms_users_table', false, 'users_form');			
			
			$button = new input('submit','button', $GLOBALS['cmdSearch'], 'javascript:'.$datatable->set_page_one().'document.users_form.submit();');
			$form->add_html_element($button);
					
			if($task == 'search')
			{		
				$datatable = new datatable('cms_users_table');
				
				$datatable->offset=25;
				
				$query = '%'.addslashes($this->query).'%';
				
				$count = $GO_USERS->search($query, '', 0, $datatable->start, $datatable->offset, "name", 'ASC');
				
				
				$datatable->set_pagination($count);
				$datatable->set_multiselect(false);
				
				$datatable->add_column(new table_heading($GLOBALS['strName']));
				$datatable->add_column(new table_heading($GLOBALS['strCompany']));
				$datatable->add_column(new table_heading($GLOBALS['strCountry']));
				$datatable->add_column(new table_heading($GLOBALS['strCity']));
				
				while($GO_USERS->next_record())
				{		
					$row = new table_row();
					
					$link = new hyperlink('javascript:show_user('.$GO_USERS->f('id').');', htmlspecialchars(format_name($GO_USERS->f('last_name'), $GO_USERS->f('first_name'), $GO_USERS->f('middle_name'), 'first_name')));
					
					$cell = new table_cell($link->get_html());
					$row->add_cell($cell);
					
					$country_text = '';
					$go_users = new $GLOBALS['go_users_class'];
					
					if($country = $go_users->get_country($GO_USERS->f('country_id')))
					{
						$country_text=$country['name'];
					}			
					$row->add_cell(new table_cell(htmlspecialchars($GO_USERS->f('company'))));
					$row->add_cell(new table_cell($country_text));
					$row->add_cell(new table_cell(htmlspecialchars($GO_USERS->f('city'))));
					
					$datatable->add_row($row);			
				}
				$form->add_html_element($datatable);
				
				
			}
			
		}
		

		return '<script type="text/javascript">function show_user(user_id){document.users_form.user_id.value=user_id;document.users_form.submit();}</script>'.$form->get_html();
	}
}
