<?php

$GO_MODULES2=new GO_MODULES();

if(file_exists($GO_CONFIG->root_path.'LICENSE.PRO'))
{
	if(!isset($GO_MODULES->modules['custom_fields']))
	{
		$module_id='custom_fields';
		$acl_read = $GO_SECURITY->get_new_acl('Module read: '.$module_id, 0);
		$acl_write = $GO_SECURITY->get_new_acl('Module write: '.$module_id, 0);		
		$module_info = $GO_MODULES2->get_module_info($module_id);
		$GO_MODULES2->add_module($module_id, $module_info['version'], $acl_read, $acl_write, $module_info['sort_order'], true);
	}
	
	if(!isset($GO_MODULES->modules['reports']))
	{
		$module_id='reports';
		$acl_read = $GO_SECURITY->get_new_acl('Module read: '.$module_id, 0);
		$acl_write = $GO_SECURITY->get_new_acl('Module write: '.$module_id, 0);		
		$module_info = $GO_MODULES2->get_module_info($module_id);
		$GO_MODULES2->add_module($module_id, $module_info['version'], $acl_read, $acl_write, $module_info['sort_order']);
	}
	$GO_MODULES2->load_modules();
	
	require($GO_LANGUAGE->get_language_file('reports'));
	
	require($GO_MODULES2->modules['custom_fields']['class_path'].'custom_fields.class.inc');
	$cf = new custom_fields();
	
	require($GO_MODULES2->modules['reports']['class_path'].'reports.class.inc');
	$reports = new reports();
	
	$db2 = new db();
	
	$category=$cf->get_category_by_name(2, $re_newsletters);
	if(!$category)
	{
		$category['name']=$re_newsletters;
		$category['type'] = 2;
		$category['acl_id'] = $GO_SECURITY->get_new_acl();	
		$category['id']=$cf->add_category($category);
	}
	$contacts_category_id=$category['id'];
	
	$category=$cf->get_category_by_name(3, $re_newsletters);
	if(!$category)
	{
		$category['name']=$re_newsletters;
		$category['type'] = 3;
		$category['acl_id'] = $GO_SECURITY->get_new_acl();	
		$category['id']=$cf->add_category($category);
	}
	$companies_category_id=$category['id'];
	
	$category=$cf->get_category_by_name(8, $re_newsletters);
	if(!$category)
	{
		$category['name']=$re_newsletters;
		$category['type'] = 8;
		$category['acl_id'] = $GO_SECURITY->get_new_acl();	
		$category['id']=$cf->add_category($category);
	}
	$users_category_id=$category['id'];
	

	$db->query('SELECT * FROM tp_mailing_groups');
	
	while($db->next_record())
	{
		
		$db2->query("SELECT id, link_id FROM tp_mailing_contacts ".
		"INNER JOIN ab_contacts ON (tp_mailing_contacts.contact_id=ab_contacts.id) ".
		"WHERE tp_mailing_contacts.group_id=".$db->f('id'));
		
		if($db2->num_rows())
		{
			$field = $cf->get_field_by_name($contacts_category_id, addslashes($db->f('name')));
			if(!$field)
			{
				$field['name'] = addslashes($db->f('name'));
				$field['datatype'] = 'checkbox';
				$field['category_id'] = $contacts_category_id;		
				$field['id']=$cf->add_field($field);
			}			
			$contacts_field_id = $field['id'];			
			
			while($db2->next_record())
			{
				$cf->get_values(2, $db2->f('link_id'));
				$cf->set_value($db2->f('link_id'),2,$contacts_field_id,'1');
			}
			
			$report=$reports->get_report_by_name(addslashes($db->f('name')));
			
			if(!$report)
			{			
				$report['user_id']=addslashes($db->f('user_id'));
				$report['name']= addslashes($db->f('name'));
				$report['type']='contacts';
				$report['acl_read']=$GO_SECURITY->get_new_acl();
				$report['acl_write']=$GO_SECURITY->get_new_acl();			
				$report['criteria']=addslashes('col_'.$contacts_field_id.' = \'1\'');			
				$report_id=$reports->add_report($report);
			
				$re_field['report_id']=$report_id;
				$re_field['field']='email';
				$re_field['colname']='E-mail';
				$re_field['sort_order']=1;
				$reports->add_field($re_field);
				
				$GO_SECURITY->copy_acl($db->f('acl_read'), $report['acl_read']);
				$GO_SECURITY->copy_acl($db->f('acl_write'), $report['acl_write']);
			}else {
				$report['criteria']=addslashes('col_'.$contacts_field_id.' = \'1\'');
				$reports->update_report($report);
			}
		}
		
		$db2->query("SELECT id, link_id FROM tp_mailing_companies ".
		"INNER JOIN ab_companies ON (tp_mailing_companies.company_id=ab_companies.id) ".
		"WHERE tp_mailing_companies.group_id=".$db->f('id'));
		
		if($db2->num_rows())
		{
			$field = $cf->get_field_by_name($companies_category_id, addslashes($db->f('name')));
			if(!$field)
			{
				$field['name'] = addslashes($db->f('name'));
				$field['datatype'] = 'checkbox';
				$field['category_id'] = $companies_category_id;		
				$field['id']=$cf->add_field($field);
			}			
			$companies_field_id = $field['id'];			
			
			while($db2->next_record())
			{
				$cf->get_values(3, $db2->f('link_id'));
				$cf->set_value($db2->f('link_id'),3,$companies_field_id,'1');
			}
			
			$report=$reports->get_report_by_name(addslashes($db->f('name')));
			
			if(!$report)
			{			
				$report['user_id']=addslashes($db->f('user_id'));
				$report['name']= addslashes($db->f('name'));
				$report['type']='companies';
				$report['acl_read']=$GO_SECURITY->get_new_acl();
				$report['acl_write']=$GO_SECURITY->get_new_acl();			
				$report['criteria']=addslashes('col_'.$companies_field_id.' = \'1\'');			
				$report_id=$reports->add_report($report);
			
				$re_field['report_id']=$report_id;
				$re_field['field']='email';
				$re_field['colname']='E-mail';
				$re_field['sort_order']=1;
				$reports->add_field($re_field);
				
				$GO_SECURITY->copy_acl($db->f('acl_read'), $report['acl_read']);
				$GO_SECURITY->copy_acl($db->f('acl_write'), $report['acl_write']);
			}else {
				$report['criteria']=addslashes('col_'.$companies_field_id.' = \'1\'');
				$reports->update_report($report);
			}
		}
		
		$db2->query("SELECT id, link_id FROM tp_mailing_users ".
		"INNER JOIN users ON (tp_mailing_users.user_id=users.id) ".
		"WHERE tp_mailing_users.group_id=".$db->f('id'));
		
		if($db2->num_rows())
		{
			$field = $cf->get_field_by_name($users_category_id, addslashes($db->f('name')));
			if(!$field)
			{
				$field['name'] = addslashes($db->f('name'));
				$field['datatype'] = 'checkbox';
				$field['category_id'] = $users_category_id;		
				$field['id']=$cf->add_field($field);
			}			
			$users_field_id = $field['id'];
			
			while($db2->next_record())
			{
				$cf->get_values(8, $db2->f('link_id'));
				$cf->set_value($db2->f('link_id'),8,$users_field_id,'1');
			}
			
			$report=$reports->get_report_by_name(addslashes($db->f('name')));
			
			if(!$report)
			{			
				$report['user_id']=addslashes($db->f('user_id'));
				$report['name']= addslashes($db->f('name'));
				$report['type']='users';
				$report['acl_read']=$GO_SECURITY->get_new_acl();
				$report['acl_write']=$GO_SECURITY->get_new_acl();			
				$report['criteria']=addslashes('col_'.$users_field_id.' = \'1\'');			
				$report_id=$reports->add_report($report);
			
				$re_field['report_id']=$report_id;
				$re_field['field']='email';
				$re_field['colname']='E-mail';
				$re_field['sort_order']=1;
				$reports->add_field($re_field);
				
				$GO_SECURITY->copy_acl($db->f('acl_read'), $report['acl_read']);
				$GO_SECURITY->copy_acl($db->f('acl_write'), $report['acl_write']);
			}else {
				$report['criteria']=addslashes('col_'.$users_field_id.' = \'1\'');
				$reports->update_report($report);
			}
		}
	}

}