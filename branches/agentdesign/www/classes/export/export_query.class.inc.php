<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */


//ini_set('display_errors', 'off');

class export_query
{
	function find_custom_exports(){
		global $GO_CONFIG;

		require_once($GO_CONFIG->class_path.'filesystem.class.inc');
		$fs = new filesystem();

		$ce=array();
		if(is_dir($GO_CONFIG->file_storage_path.'customexports')){
			$files = $fs->get_files($GO_CONFIG->file_storage_path.'customexports');
			while($file = array_shift($files)){
				require_once($file['path']);

				$names = explode('.', $file['name']);

				$cls = new $names[0];

				if(!isset($ce[$cls->query]))
					$ce[$cls->query]=array();

				$ce[$cls->query][]=array('name'=>$cls->name, 'cls'=>$names[0]);
			}
		}

		return 'GO.customexports='.json_encode($ce).';';
	}	
}

class base_export_query{
	var $db;
	var $query_name;
	var $q;
	var $totals = array();
	var $title='';
	var $extension='';

	function __construct(){

		$this->db = new db();
		$this->query_name=$_REQUEST['query'];
		$this->q = $_SESSION['GO_SESSION']['export_queries'][$_REQUEST['query']];

		if(!isset($this->q['totalize_columns']))
			$this->q['totalize_columns']=array();
		
		$this->title = $_REQUEST['title'];
	}

	function download_headers()
	{

	}

	function query(){
		$params = array();
		$types='';

		if(is_array($this->q))
		{
			if(!empty($this->q['require']))
			{
				require_once($this->q['require']);
			}

			$this->totals=array();
			if(isset($this->q['totalize_columns']))
			{
				foreach($this->q['totalize_columns'] as $column){
					$this->totals[$column]=0;
				}
			}else
			{
				unset($this->totals);
			}

			$extra_sql=array();
			$sql = $this->q['query'];
			if(isset($this->q['extra_params']))
			{
				foreach($this->q['extra_params'] as $param=>$sqlpart)
				{
					if(!empty($_REQUEST[$param]))
					{
						$params[] = $_REQUEST[$param];
						$extra_sql[]=$sqlpart;
					}
				}
			}
			if(count($params))
			{
				$insert = ' ';
				if(!strpos($sql, 'WHERE'))
				{
					$insert .= 'WHERE ';
				}else
				{
					$insert .= ' AND ';
				}
				$insert .= implode(' AND ', $extra_sql);

				$pos = strpos($sql, 'ORDER');

				if(!$pos)
				{
					$sql .= $insert;
				}else
				{
					$sql = substr($sql, 0, $pos).$insert.' '.substr($sql, $pos);
				}

				$types=str_repeat('s',count($params));
			}
		}else
		{
			$sql = $this->q;

			$params=array();
		}

		$GLOBALS['GO_EVENTS']->fire_event('export_before_query', array(&$this, &$sql, &$types, &$params));

		$this->db->query($sql,$types,$params);
	}

	function format_record(&$record){
		if(is_array($this->q) && isset($this->q['method']))
		{
			call_user_func_array(array($this->q['class'], $this->q['method']),array(&$record, $this->cf));
		}
		
		$GLOBALS['GO_EVENTS']->fire_event('export_format_record', array(&$this, &$record));
	}

	function init_columns(){
		$this->columns=array();
		$this->headers=array();
		if(isset($_REQUEST['columns']))
		{
			$indexesAndHeaders = explode(',', $_REQUEST['columns']);

			foreach($indexesAndHeaders as $i)
			{
				$indexAndHeader = explode(':', $i);

				if(!isset($this->q['hide_columns']) || !in_array($indexAndHeader[0], $this->q['hide_columns'])){
					$this->headers[]=$indexAndHeader[1];
					$this->columns[]=$indexAndHeader[0];
				}
			}
		}

		if(isset($this->q['extra_columns'])){
			foreach($this->q['extra_columns'] as $column){
				if(!isset($column['index']))
					$column['index']=count($this->headers);

				array_insert($this->headers,$column['header'], $column['index']);
				array_insert($this->columns,$column['column'], $column['index']);
			}
		}

		$GLOBALS['GO_EVENTS']->fire_event('export_init_columns', array(&$this));
	}

	function export($fp)
	{
		global $GO_MODULES;

		if($GO_MODULES->has_module('customfields')) {
			require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
			$this->cf = new customfields();
		}else
		{
			$this->cf=false;
		}

		$this->query();
		$this->init_columns();
	}


	function increase_totals($record){
		foreach($this->q['totalize_columns'] as $column){
			if(isset($record[$column]))
				$this->totals[$column]+=$record[$column];
		}
	}
}