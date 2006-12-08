<?php
class cms_account {

	var $attributes;
	var $cms_site;

	function cms_account($attributes, &$cms_site)
	{
		$this->attributes = $attributes;
		$this->cms_site=&$cms_site;
	}

	function has_permission()
	{
		global $GO_MODULES;
		return true;
	}

	function get_name()
	{
		global $GO_LANGUAGE;
		require($GO_LANGUAGE->get_language_file('billing'));
		return $bs_account;
	}

	function get_header()
	{
	}

	function check_fields($required_fields)
	{

		foreach($required_fields as $field)
		{
			if(empty($_POST[$field]))
			{
				return false;
			}
		}

		return true;
	}

	function get_html()
	{

		global $GO_SECURITY, $GO_MODULES, $GO_CONFIG, $GO_LANGUAGE, $GO_USERS, $GO_LINKS;

		$this->cms_site->authenticate();

		$fields = isset($this->attributes['fields']) ? $this->attributes['fields'] : $GO_CONFIG->registration_fields;
		$fields = explode(',', $fields);


		$required_registration_fields = str_replace('address', 'address,address_no,zip,city,state,country_id', $GO_CONFIG->required_registration_fields);
		$required_registration_fields = str_replace('work_address', 'work_address,work_address_no,work_zip,work_city,work_state,work_country_id', $required_registration_fields);
		$required_registration_fields = str_replace('title_initials', 'title,initioals', $required_registration_fields);
		$req_fields = explode(',',$required_registration_fields);
		$req_fields[]='email';
		$req_fields[]='first_name';
		$req_fields[]='last_name';

		$disabled_user_fields = $GO_CONFIG->get_setting('disabled_user_fields');

		if(!$disabled_user_fields)
		{
			$disabled_user_fields=array();
		}else {
			$disabled_user_fields = explode(',', $disabled_user_fields);
		}

		$required_fields=array();
		foreach($req_fields as $field)
		{
			if(!in_array($field, $disabled_user_fields))
			{
				$required_fields[]=$field;
			}
		}


		$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';


		if($task =='save')
		{
			$enabled_fields = array();

			foreach($fields as $field)
			{
				if(!in_array($field, $disabled_user_fields))
				{
					$enabled_fields[]=$field;
				}
			}
			$user['id']=$GO_SECURITY->user_id;
			$user['first_name'] = isset($_POST['first_name']) ?  smart_addslashes(trim($_POST['first_name'])) : '';
			$user['middle_name'] = isset($_POST['middle_name']) ?  smart_addslashes(trim($_POST['middle_name'])) : '';
			$user['last_name'] = isset($_POST['last_name']) ?  smart_addslashes(trim($_POST['last_name'])) : '';

			if(in_array('title_initials', $enabled_fields))
			{
				$user['initials'] = isset($_POST['initials']) ? smart_addslashes($_POST["initials"]) : '';
				$user['title'] = isset($_POST['title']) ? smart_addslashes($_POST["title"]) : '';
			}
			if(in_array('birthday', $enabled_fields))
			{
				$user['birthday'] = isset($_POST['birthday']) ? smart_addslashes($_POST["birthday"]) : '';
				$user['birthday'] = date_to_db_date($user['birthday']);
			}
			$user['email'] = isset($_POST['email']) ? smart_addslashes($_POST["email"]) : '';
			if(in_array('home_phone', $enabled_fields))
			{
				$user['home_phone'] = isset($_POST['home_phone']) ? smart_addslashes($_POST["home_phone"]) : '';
			}
			if(in_array('work_phone', $enabled_fields))
			{
				$user['work_phone'] = isset($_POST['work_phone']) ? smart_addslashes($_POST["work_phone"]) : '';
			}
			if(in_array('fax', $enabled_fields))
			{
				$user['fax'] = isset($_POST['fax']) ? smart_addslashes($_POST["fax"]) : '';
			}
			if(in_array('work_fax', $enabled_fields))
			{
				$user['work_fax'] = isset($_POST['work_fax']) ? smart_addslashes($_POST["work_fax"]) : '';
			}
			if(in_array('cellular', $enabled_fields))
			{
				$user['cellular'] = isset($_POST['cellular']) ? smart_addslashes($_POST["cellular"]) : '';
			}
			if(in_array('address', $enabled_fields))
			{
				$user['country_id'] = isset($_POST['country_id']) ? smart_addslashes($_POST["country_id"]) : '';
				$user['state'] = isset($_POST['state']) ? smart_addslashes($_POST["state"]) : '';
				$user['city'] = isset($_POST['city']) ? smart_addslashes($_POST["city"]) : '';
				$user['zip'] = isset($_POST['zip']) ? smart_addslashes($_POST["zip"]) : '';
				$user['address'] = isset($_POST['address']) ? smart_addslashes($_POST["address"]) : '';
				$user['address_no'] = isset($_POST['address_no']) ? smart_addslashes($_POST["address_no"]) : '';
			}

			if(in_array('work_address', $enabled_fields))
			{
				$user['work_country_id'] = isset($_POST['work_country_id']) ? smart_addslashes($_POST["work_country_id"]) : '';
				$user['work_state'] = isset($_POST['work_state']) ? smart_addslashes($_POST["work_state"]) : '';
				$user['work_city'] = isset($_POST['work_city']) ? smart_addslashes($_POST["work_city"]) : '';
				$user['work_zip'] = isset($_POST['work_zip']) ? smart_addslashes($_POST["work_zip"]) : '';
				$user['work_address'] = isset($_POST['work_address']) ? smart_addslashes($_POST["work_address"]) : '';
				$user['work_address_no'] = isset($_POST['work_address_no']) ? smart_addslashes($_POST["work_address_no"]) : '';
			}

			if(in_array('company', $enabled_fields))
			{
				$user['company'] = isset($_POST['company']) ? smart_addslashes($_POST["company"]) : '';
			}
			if(in_array('department', $enabled_fields))
			{
				$user['department'] =  isset($_POST['department']) ? smart_addslashes($_POST["department"]) : '';
			}
			if(in_array('function', $enabled_fields))
			{
				$user['function'] =  isset($_POST['function']) ? smart_addslashes($_POST["function"]) : '';
			}
			if(in_array('sex', $enabled_fields))
			{
				$user['sex'] = isset($_POST['sex']) ? smart_addslashes($_POST["sex"]) : 'M';
			}

			if(in_array('homepage', $enabled_fields))
			{
				$user['homepage'] = isset($_POST['homepage']) ? smart_addslashes($_POST["homepage"]) : '';
			}


			if (!$this->check_fields($required_fields))
			{
				$feedback = $GLOBALS['error_missing_field'];
			}elseif(!validate_email($user['email']))
			{
				$feedback = $GLOBALS['error_email'];
			}else
			{
				$existing_user = $GO_USERS->get_user_by_email($user['email']);

				if($existing_user['id']!=$GO_SECURITY->user_id)
				{
					$feedback = $GLOBALS['error_email_exists'];
				}else
				{
					$GO_USERS->update_user($user);
				}
			}
			$user=array_map('stripslashes', $user);
		}else {
			$user = $GO_USERS->get_user($GO_SECURITY->user_id);
		}

		$birthday = db_date_to_date($user['birthday']);
		$birthday_picker = new date_picker('birthday', $_SESSION['GO_SESSION']['date_format'], $birthday);
		$form->outerHTML .= $birthday_picker->get_header();





		$form = new form('account_form');
		$form->add_html_element(new input('hidden','task', '',false));
		$form->add_html_element(new input('hidden','file_id', $this->cms_site->file_id));
		$form->add_html_element(new input('hidden','folder_id', $this->cms_site->folder_id));

		if (isset($feedback))
		{
			$p = new html_element('p', $feedback);
			$p->set_attribute('style','color:red');
			$form->add_html_element($p);
		}


		$table = new table();

		$row = new table_row();
		$row->add_cell(new table_cell($GLOBALS['strFirstName'].'*:'));
		$input = new input('text','first_name',$user['first_name']);
		if(in_array('first_name', $disabled_user_fields))
		{
			$input->set_attribute('disabled', 'true');
		}
		$input->set_attribute('style','width:280px');
		$input->set_attribute('maxlength','50');
		$row->add_cell(new table_cell($input->get_html()));
		$table->add_row($row);

		$row = new table_row();
		$row->add_cell(new table_cell($GLOBALS['strMiddleName'].':'));
		$input = new input('text','middle_name',$user['middle_name']);
		if(in_array('middle_name', $disabled_user_fields))
		{
			$input->set_attribute('disabled', 'true');
		}
		$input->set_attribute('style','width:280px');
		$input->set_attribute('maxlength','50');
		$row->add_cell(new table_cell($input->get_html()));
		$table->add_row($row);


		$row = new table_row();
		$row->add_cell(new table_cell($GLOBALS['strLastName'].'*:'));
		$input = new input('text','last_name', $user['last_name']);
		if(in_array('last_name', $disabled_user_fields))
		{
			$input->set_attribute('disabled', 'true');
		}
		$input->set_attribute('style','width:280px');
		$input->set_attribute('maxlength','50');
		$row->add_cell(new table_cell($input->get_html()));
		$table->add_row($row);




		if(in_array('title_initials',$fields))
		{
			$row = new table_row();


			$end=in_array('inititals_title',$required_fields)? '*:' : ':';
			$row->add_cell(new table_cell($GLOBALS['strTitle'].' / '.$GLOBALS['strInitials'].$end));

			$input1 = new input('text','title', $user['title']);
			$input1->set_attribute('style','width:135px');
			$input1->set_attribute('maxlength','12');
			if(in_array('title', $disabled_user_fields))
			{
				$input1->set_attribute('disabled', 'true');
			}

			$span = new html_element('span', ' / ');
			$span->set_attribute('style', 'width: 20px;text-align:center;');

			$input2 = new input('text','initials', $user['initials']);
			$input2->set_attribute('style','width:135px');
			$input2->set_attribute('maxlength','50');
			if(in_array('initials', $disabled_user_fields))
			{
				$input2->set_attribute('disabled', 'true');
			}

			$row->add_cell(new table_cell($input1->get_html().$span->get_html().$input2->get_html()));
			$table->add_row($row);
		}



		if(in_array('sex',$fields))
		{
			$row = new table_row();
			$end=in_array('sex',$required_fields)? '*:' : ':';
			$row->add_cell(new table_cell($GLOBALS['strSex'].$end));
			if(!in_array('sex', $disabled_user_fields))
			{
				$radiogroup = new radiogroup('sex', $user['sex']);
				$male_button = new radiobutton('sex_m', 'M');
				$female_button = new radiobutton('sex_f', 'F');

				$row->add_cell(new table_cell($radiogroup->get_option($male_button, $GLOBALS['strSexes']['M']).$radiogroup->get_option($female_button, $GLOBALS['strSexes']['F'])));
				$table->add_row($row);
			}else {
				$row->add_cell(new table_cell($GLOBALS['strSexes'][$user['sex']]));
				$table->add_row($row);
			}
		}

		if(in_array('birthday',$fields))
		{
			$end=in_array('birthday',$required_fields)? '*:' : ':';

			$row = new table_row();
			$row->add_cell(new table_cell($GLOBALS['strBirthday'].$end));
			if(in_array('birthday', $disabled_user_fields))
			{
				$row->add_cell(new table_cell($birthday));
			}else {
				$row->add_cell(new table_cell($birthday_picker->get_html()));
			}
			$table->add_row($row);
		}

		$row = new table_row();
		$row->add_cell(new table_cell($GLOBALS['strEmail'].'*:'));
		$input = new input('text','email', $user['email']);
		$input->set_attribute('style','width:280px');
		$input->set_attribute('maxlength','50');
		$row->add_cell(new table_cell($input->get_html()));
		$table->add_row($row);

		$row = new table_row();
		$cell = new table_cell('&nbsp;');
		$cell->set_attribute('colspan','2');
		$row->add_cell($cell);
		$table->add_row($row);

		if(in_array('address',$fields))
		{
			$end=in_array('address',$required_fields)? '*:' : ':';

			$row = new table_row();
			$row->add_cell(new table_cell($GLOBALS['strAddress'].$end));
			$input = new input('text','address', $user['address']);
			$input->set_attribute('style','width:230px');
			$input->set_attribute('maxlength','50');
			if(in_array('address', $disabled_user_fields))
			{
				$input->set_attribute('disabled', 'true');
			}

			$input1 = new input('text','address_no', $user['address_no']);
			$input1->set_attribute('style','width:47px');
			$input1->set_attribute('maxlength','10');
			if(in_array('address_no', $disabled_user_fields))
			{
				$input1->set_attribute('disabled', 'true');
			}

			$row->add_cell(new table_cell($input->get_html().$input1->get_html()));
			$table->add_row($row);

			$end=in_array('zip',$required_fields)? '*:' : ':';
			$row = new table_row();
			$row->add_cell(new table_cell($GLOBALS['strZip'].$end));
			$input = new input('text','zip', $user['zip']);
			$input->set_attribute('style','width:280px');
			$input->set_attribute('maxlength','20');
			if(in_array('zip', $disabled_user_fields))
			{
				$input->set_attribute('disabled', 'true');
			}
			$row->add_cell(new table_cell($input->get_html()));
			$table->add_row($row);

			$end=in_array('city',$required_fields)? '*:' : ':';
			$row = new table_row();
			$row->add_cell(new table_cell($GLOBALS['strCity'].$end));
			$input = new input('text','city', $user['city']);
			$input->set_attribute('style','width:280px');
			$input->set_attribute('maxlength','50');
			if(in_array('city', $disabled_user_fields))
			{
				$input->set_attribute('disabled', 'true');
			}
			$row->add_cell(new table_cell($input->get_html()));
			$table->add_row($row);

			$end=in_array('state',$required_fields)? '*:' : ':';
			$row = new table_row();
			$row->add_cell(new table_cell($GLOBALS['strState'].$end));
			$input = new input('text','state', $user['state']);
			$input->set_attribute('style','width:280px');
			$input->set_attribute('maxlength','30');
			if(in_array('state', $disabled_user_fields))
			{
				$input->set_attribute('disabled', 'true');
			}
			$row->add_cell(new table_cell($input->get_html()));
			$table->add_row($row);

			$end=in_array('country_id',$required_fields)? '*:' : ':';
			$row = new table_row();
			$row->add_cell(new table_cell($GLOBALS['strCountry'].$end));
			$select = new select('country_id', $user['country_id']);
			$select->add_value('0', $GLOBALS['cmdPleaseSelect']);
			$GO_USERS->get_countries();
			while($GO_USERS->next_record())
			{
				$select->add_value($GO_USERS->f('id'), $GO_USERS->f('name'));
			}
			if(in_array('country_id', $disabled_user_fields))
			{
				$select->set_attribute('disabled', 'true');
			}
			$row->add_cell(new table_cell($select->get_html()));
			$table->add_row($row);

			$row = new table_row();
			$cell = new table_cell('&nbsp;');
			$cell->set_attribute('colspan','2');
			$row->add_cell($cell);
			$table->add_row($row);
		}

		$break=false;
		if(in_array('home_phone',$fields))
		{
			$end=in_array('home_phone',$required_fields)? '*:' : ':';
			$row = new table_row();
			$row->add_cell(new table_cell($GLOBALS['strPhone'].$end));
			$input = new input('text','home_phone', $user['home_phone']);
			$input->set_attribute('style','width:280px');
			$input->set_attribute('maxlength','20');
			if(in_array('home_phone', $disabled_user_fields))
			{
				$input->set_attribute('disabled', 'true');
			}
			$row->add_cell(new table_cell($input->get_html()));
			$table->add_row($row);
			$break=true;
		}

		if(in_array('fax',$fields))
		{
			$end=in_array('fax',$required_fields)? '*:' : ':';
			$row = new table_row();
			$row->add_cell(new table_cell($GLOBALS['strFax'].$end));
			$input = new input('text','fax', $user['fax']);
			$input->set_attribute('style','width:280px');
			$input->set_attribute('maxlength','20');
			if(in_array('fax', $disabled_user_fields))
			{
				$input->set_attribute('disabled', 'true');
			}
			$row->add_cell(new table_cell($input->get_html()));
			$table->add_row($row);
			$break=true;
		}
		if(in_array('cellular',$fields))
		{
			$end=in_array('cellular',$required_fields)? '*:' : ':';
			$row = new table_row();
			$row->add_cell(new table_cell($GLOBALS['strCellular'].$end));
			$input = new input('text','cellular', $user['cellular']);
			$input->set_attribute('style','width:280px');
			$input->set_attribute('maxlength','20');
			if(in_array('cellular', $disabled_user_fields))
			{
				$input->set_attribute('disabled', 'true');
			}
			$row->add_cell(new table_cell($input->get_html()));
			$table->add_row($row);
			$break=true;
		}

		if($break)
		{
			$row = new table_row();
			$cell = new table_cell('&nbsp;');
			$cell->set_attribute('colspan','2');
			$row->add_cell($cell);
			$table->add_row($row);
			$break=false;
		}



		$break = false;
		if(in_array('company',$fields))
		{
			$end=in_array('company',$required_fields)? '*:' : ':';
			$row = new table_row();
			$row->add_cell(new table_cell($GLOBALS['strCompany'].$end));
			$input = new input('text','company', $user['company']);
			$input->set_attribute('style','width:280px');
			$input->set_attribute('maxlength','50');
			if(in_array('company', $disabled_user_fields))
			{
				$input->set_attribute('disabled', 'true');
			}
			$row->add_cell(new table_cell($input->get_html()));
			$table->add_row($row);
			$break=true;
		}

		if(in_array('department',$fields))
		{
			$end=in_array('department',$required_fields)? '*:' : ':';
			$row = new table_row();
			$row->add_cell(new table_cell($GLOBALS['strDepartment'].$end));
			$input = new input('text','department', $user['department']);
			$input->set_attribute('style','width:280px');
			$input->set_attribute('maxlength','50');
			if(in_array('department', $disabled_user_fields))
			{
				$input->set_attribute('disabled', 'true');
			}
			$row->add_cell(new table_cell($input->get_html()));
			$table->add_row($row);
			$break=true;
		}

		if(in_array('function',$fields))
		{
			$end=in_array('function',$required_fields)? '*:' : ':';
			$row = new table_row();
			$row->add_cell(new table_cell($GLOBALS['strFunction'].$end));
			$input = new input('text','function', $user['function']);
			$input->set_attribute('style','width:280px');
			$input->set_attribute('maxlength','50');
			if(in_array('function', $disabled_user_fields))
			{
				$input->set_attribute('disabled', 'true');
			}
			$row->add_cell(new table_cell($input->get_html()));
			$table->add_row($row);
			$break=true;
		}

		if($break)
		{
			$row = new table_row();
			$cell = new table_cell('&nbsp;');
			$cell->set_attribute('colspan','2');
			$row->add_cell($cell);
			$table->add_row($row);
			$break=false;
		}

		if(in_array('work_address',$fields))
		{
			$end=in_array('work_address',$required_fields)? '*:' : ':';
			$row = new table_row();
			$row->add_cell(new table_cell($GLOBALS['strWorkAddress'].$end));
			$input = new input('text','work_address', $user['work_address']);
			$input->set_attribute('style','width:230px');
			$input->set_attribute('maxlength','50');
			if(in_array('work_address', $disabled_user_fields))
			{
				$input->set_attribute('disabled', 'true');
			}

			$input1 = new input('text','work_address_no', $user['work_address_no']);
			$input1->set_attribute('style','width:47px');
			$input1->set_attribute('maxlength','10');
			if(in_array('work_address_no', $disabled_user_fields))
			{
				$input1->set_attribute('disabled', 'true');
			}

			$row->add_cell(new table_cell($input->get_html().$input1->get_html()));
			$table->add_row($row);

			$end=in_array('work_zip',$required_fields)? '*:' : ':';
			$row = new table_row();
			$row->add_cell(new table_cell($GLOBALS['strWorkZip'].$end));
			$input = new input('text','work_zip', $user['work_zip']);
			$input->set_attribute('style','width:280px');
			$input->set_attribute('maxlength','20');
			if(in_array('work_zip', $disabled_user_fields))
			{
				$input->set_attribute('disabled', 'true');
			}
			$row->add_cell(new table_cell($input->get_html()));
			$table->add_row($row);

			$end=in_array('work_city',$required_fields)? '*:' : ':';
			$row = new table_row();
			$row->add_cell(new table_cell($GLOBALS['strWorkCity'].$end));
			$input = new input('text','work_city', $user['work_city']);
			$input->set_attribute('style','width:280px');
			$input->set_attribute('maxlength','50');
			if(in_array('work_city', $disabled_user_fields))
			{
				$input->set_attribute('disabled', 'true');
			}
			$row->add_cell(new table_cell($input->get_html()));
			$table->add_row($row);

			$end=in_array('work_state',$required_fields)? '*:' : ':';
			$row = new table_row();
			$row->add_cell(new table_cell($GLOBALS['strWorkState'].$end));
			$input = new input('text','work_state', $user['work_state']);
			$input->set_attribute('style','width:280px');
			$input->set_attribute('maxlength','30');
			if(in_array('work_state', $disabled_user_fields))
			{
				$input->set_attribute('disabled', 'true');
			}
			$row->add_cell(new table_cell($input->get_html()));
			$table->add_row($row);

			$end=in_array('inititals_title',$required_fields)? '*:' : ':';
			$row = new table_row();
			$row->add_cell(new table_cell($GLOBALS['strCountry'].$end));


			$select = new select('work_country_id', $user['work_country_id']);
			$select->add_value('0', $GLOBALS['cmdPleaseSelect']);
			$GO_USERS->get_countries();
			while($GO_USERS->next_record())
			{
				$select->add_value($GO_USERS->f('id'), $GO_USERS->f('name'));
			}
			if(in_array('work_country_id', $disabled_user_fields))
			{
				$input->set_attribute('disabled', 'true');
			}
			$row->add_cell(new table_cell($select->get_html()));
			$table->add_row($row);

			$row = new table_row();
			$cell = new table_cell('&nbsp;');
			$cell->set_attribute('colspan','2');
			$row->add_cell($cell);
			$table->add_row($row);
		}

		if(in_array('work_phone',$fields))
		{
			$end=in_array('work_phone',$required_fields)? '*:' : ':';
			$row = new table_row();
			$row->add_cell(new table_cell($GLOBALS['strWorkphone'].$end));
			$input = new input('text','work_phone', $user['work_phone']);
			$input->set_attribute('style','width:280px');
			$input->set_attribute('maxlength','20');
			if(in_array('work_phone', $disabled_user_fields))
			{
				$input->set_attribute('disabled', 'true');
			}
			$row->add_cell(new table_cell($input->get_html()));
			$table->add_row($row);
		}

		if(in_array('work_fax',$fields))
		{
			$end=in_array('work_fax',$required_fields)? '*:' : ':';
			$row = new table_row();
			$row->add_cell(new table_cell($GLOBALS['strWorkFax'].$end));
			$input = new input('text','work_fax', $user['work_fax']);
			$input->set_attribute('style','width:280px');
			$input->set_attribute('maxlength','20');
			if(in_array('work_fax', $disabled_user_fields))
			{
				$input->set_attribute('disabled', 'true');
			}
			$row->add_cell(new table_cell($input->get_html()));
			$table->add_row($row);
		}

		if(in_array('homepage',$fields))
		{
			$end=in_array('homepage',$required_fields)? '*:' : ':';
			$row = new table_row();
			$row->add_cell(new table_cell($GLOBALS['strHomepage'].$end));
			$input = new input('text','homepage', $user['homepage']);
			$input->set_attribute('style','width:280px');
			$input->set_attribute('maxlength','20');
			if(in_array('homepage', $disabled_user_fields))
			{
				$input->set_attribute('disabled', 'true');
			}
			$row->add_cell(new table_cell($input->get_html()));
			$table->add_row($row);
		}

		$form->add_html_element($table);


		$input = new input('button','cmdSave',$GLOBALS['cmdSave']);
		$input->set_attribute('onclick',"javascript:document.account_form.task.value='save';document.account_form.submit();");
		$form->add_html_element($input);


		return $form->get_html();
	}
}
?>