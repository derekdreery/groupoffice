<?php
require(GO::config()->class_path.'export/csv_export_query.class.inc.php');
class with_companies_export_query extends csv_export_query{

	function init_company_columns()
	{
		$this->company_columns=array();
		if(isset($_REQUEST['companyColumns']))
		{
			$indexesAndHeaders = explode(',', $_REQUEST['companyColumns']);

			foreach($indexesAndHeaders as $i)
			{
				$indexAndHeader = explode(':', $i);

				$this->headers[]=$indexAndHeader[1];
				$this->company_columns[]=$indexAndHeader[0];
			}
		}
	}

	function export($fp){
		global $lang, $GO_MODULES, $GO_SECURITY, $GO_CONFIG;

		require_once(GO::config()->class_path.'base/users.class.inc.php');
		$GO_USERS = new GO_USERS();


		if(GO::modules()->has_module('customfields')) {
			require_once(GO::modules()->modules['customfields']['class_path'].'customfields.class.inc.php');
			$this->cf = new customfields();
		}else
		{
			$this->cf=false;
		}
		
		$this->query();
		$this->init_columns();
		$this->init_company_columns();

		if(count($this->headers))
			$this->fputcsv($fp, $this->headers, $this->list_separator, $this->text_separator);

		$books = isset($_REQUEST['books']) ? $_REQUEST['books'] : array();
		if(count($books))
		{
			require_once(GO::modules()->modules['addressbook']['class_path'].'addressbook.class.inc.php');
			$ab = new addressbook();
			$ab2 = new addressbook();

			$ab->search_contacts(GO::security()->user_id,  '', $books);
		
			while($record = $this->db->next_record())
			{
				$this->format_record($record);

				if(isset($record['user_id']) && isset($this->columns['user_id']))
				{
					$record['user_id']= $GO_USERS->get_user_realname($record['user_id']);
				}
				$values=array();
				foreach($this->columns as $index)
				{
					$values[] = $record[$index];
				}

				if($record['company_id'])
				{
					$company = $ab2->get_company($record['company_id']);
					foreach($this->company_columns as $index)
					{
					    if(isset($company[$index]))
						$values[] = $company[$index];
					}
				}else
				{
					foreach($this->company_columns as $index)
					{
						$values[] = '';
					}
				}	
				
				$this->fputcsv($fp, $values,$this->list_separator, $this->text_separator);
			}

		}

		
		
	}
	
}