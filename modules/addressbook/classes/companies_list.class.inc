<?php
/**
 * @copyright Intermesh 2006
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.3 $ $Date: 2006/10/18 13:29:17 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
 
class companies_list extends datatable
{
	var $count;
	
	var $form_name;
	var $return_zero;
	var $return_to;
	var $links;
	var $addressbook_id;
	
	function companies_list($id, $addressbook_id, $link_id=0, $return_zero=true, $form_name='0', $return_to='')
	{
		global $GO_CONFIG, $GO_SECURITY, $GO_MODULES, $GO_LINKS, $GO_LANGUAGE;
		
		$this->datatable($id, false, $form_name);
		$this->set_attribute('style','width:100%;margin-bottom:20px;margin-top:0px;');
			
		$this->return_to = !empty($return_to) ? $return_to : $_SERVER['PHP_SELF'];		
		
		$this->return_zero=$return_zero;
		$this->form_name=$form_name;
		$this->addressbook_id=$addressbook_id;
		
		
		
		
		require($GO_LANGUAGE->get_language_file('addressbook'));

		require_once($GO_MODULES->modules['addressbook']['class_path']."addressbook.class.inc");
		$ab = new addressbook();
		
		if($this->task == 'delete')
		{
			$addressbook = $ab->get_addressbook($this->addressbook_id);
			
			if ($GO_SECURITY->has_permission($GO_SECURITY->user_id, $addressbook['acl_write']))
			{
				
				foreach($this->selected as $company_id)
				{
					$company_id =str_replace('3:', '', $company_id);
					$ab->delete_company($company_id);
				}
			}else
			{
				$feedback = $GLOBALS['strAccessDenied'];
			}	
		}elseif($this->task=='unlink')
		{
			global $GO_LINKS;
			
			foreach($this->selected as $company_id)
			{
				$company_id =str_replace('3:', '', $company_id);
				if($company = $ab->get_company($company_id))
				{					
					$GO_LINKS->delete_link($link_id, $company['link_id']);
				}
			}
		}
		$this->links = $link_id > 0 ? $GO_LINKS->get_links($link_id, 3) : false;
	}
	
	function get_html()
	{		
		global $GO_LANGUAGE, $GO_MODULES;
		
		$ab = new addressbook();
		
		
		if (isset($feedback))
		{
			$p = new html_element('p', $feedback);
			$p->set_attribute('class','Error');
			$this->add_outerhtml_element($p);
		}		
		
		$this->add_column(new table_heading($GLOBALS['strName'], 'name'));
		$this->add_column(new table_heading($GLOBALS['strCity'], 'city'));
		$this->add_column(new table_heading($GLOBALS['strEmail'], 'email'));
		$this->add_column(new table_heading($GLOBALS['strPhone'], 'phone'));
		$this->add_column(new table_heading($GLOBALS['strHomepage'], 'homepage'));
		$this->count = $ab->get_companies($this->addressbook_id, $this->sort_index, $this->sql_sort_order, $this->start, $this->offset, $this->links);
		
		$this->set_pagination($this->count);
		
		if ($this->count > 0)
		{
			while($ab->next_record())
			{	
				$full_email = $ab->f('email') == '' ? '' : '"'.$ab->f('name').'" <'.$ab->f('email').'>';

				$row = new table_row('3:'.$ab->f('id'));
				$row->set_attribute('ondblclick', "document.location='".$GO_MODULES->modules['addressbook']['url']."company.php?company_id=".$ab->f("id")."&return_to=".urlencode($this->return_to)."'");
				$cell = new table_cell(htmlspecialchars($ab->f('name')));
				if ($ab->f('color') != '')
				{
					$cell->set_attribute('style','color: '.$ab->f('color'));
				}
				$row->add_cell($cell);
				$row->add_cell(new table_cell($ab->f('city')));
				$row->add_cell(new table_cell(mail_to(empty_to_stripe(addslashes($full_email)), empty_to_stripe($ab->f('email')),'normal',true, $ab->f('id'))));
				$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($ab->f('phone')))));
				
				$hyperlink = new hyperlink($ab->f('homepage'), $ab->f('homepage'));
				$hyperlink->set_attribute('class', 'normal');
				$hyperlink->set_attribute('target', '_blank');
				
				$row->add_cell(new table_cell($hyperlink->get_html()));
				$this->add_row($row);		
			}	
		}else
		{
			if($this->return_zero)
			{
				require($GO_LANGUAGE->get_language_file('addressbook'));
				$row = new table_row();
				$cell = new table_cell($ab_no_companies);
				$cell->set_attribute('colspan','99');
				$row->add_cell($cell);
				$this->add_row($row);								
			}else
			{
				$this->innerHTML = '';
			}			
		}
		return parent::get_html();		
	}
}
