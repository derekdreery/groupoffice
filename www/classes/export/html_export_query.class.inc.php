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

class html_export_query extends base_export_query
{
	var $extension='html';
	var $list_separator=';';

	var $text_separator='"';


	function download_headers()
	{
		//$browser = detect_browser();
		header("Content-type: text/html;charset=UTF-8");
		/*if ($browser['name'] == 'MSIE')
		{
			header('Content-Disposition: inline; filename="'.$this->title.'.html"');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
		} else {
			header('Pragma: no-cache');
			header('Content-Disposition: attachment; filename="'.$this->title.'.html"');
		}*/
	}

	function export($fp){

		parent::export($fp);

		global $GO_USERS, $lang, $GO_MODULES;

		fwrite($fp, '<html>
<head>
<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />
<style>
body{
	font:12px helvetica;
}
table{
	border-collapse:collapse;
}
td, th{
	margin:0px;
	padding:1px 3px;
}
</style>
</head>
<body>');

		fwrite($fp,'<h1>'.$this->title.'</h1>');

		fwrite($fp,'<table border="1">');

		if(count($this->headers))
			fwrite($fp,'<tr><th>'.implode('</th><th>', $this->headers).'</th></tr>');


		while($record = $this->db->next_record())
		{
			$this->increase_totals($record);
			
			if(!count($this->columns))
			{
				foreach($record as $key=>$value)
				{
					$this->columns[]=$key;
					$this->headers[]=$key;
				}
				//$this->fputcsv($fp, $this->headers, $this->list_separator, $this->text_separator);
				fwrite($fp,'<tr><th>'.implode('</th><th>', $this->headers).'</th></tr>');
			}

			$this->format_record($record);

			if(isset($record['user_id']) && isset($this->columns['user_id']))
			{
				$user = $GO_USERS->get_user($record['user_id']);
				$record['user_id']=$user['username'];
			}
			$values=array();
			foreach($this->columns as $index)
			{
				$values[] = $record[$index];
			}
			fwrite($fp, '<tr><td>'.implode('</td><td>', $values).'</td></tr>');
		
		}

		if(isset($this->totals) && count($this->totals))
		{
			fwrite($fp, '<tr><td colspan="'.count($this->columns).'"><br /><b>'.$lang['common']['totals'].':</b></td></tr>');
			fwrite($fp, '<tr>');
			foreach($this->columns as $index)
			{
				$value = isset($this->totals[$index]) ? Number::format($this->totals[$index]) : '';
				fwrite($fp, '<td>'.$value.'</td>');
			}
			fwrite($fp, '</tr>');
		}

		fwrite($fp, '</table>');

		fwrite($fp, '</body></html>');
		//fclose($fp);
	}
}
