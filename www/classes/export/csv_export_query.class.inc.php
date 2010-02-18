<?php
class csv_export_query extends base_export_query
{
	var $list_separator=';';
	var $text_separator='"';

	function __construct()
	{
		parent::__construct();

		$this->list_separator=$_SESSION['GO_SESSION']['list_separator'];
		$this->text_separator=$_SESSION['GO_SESSION']['text_separator'];
	}

	function download_headers()
	{
		$browser = detect_browser();
		header("Content-type: text/x-csv;charset=UTF-8");
		if ($browser['name'] == 'MSIE')
		{
			header('Content-Disposition: inline; filename="'.$this->title.'.csv"');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
		} else {
			header('Pragma: no-cache');
			header('Content-Disposition: attachment; filename="'.$this->title.'.csv"');
		}
	}

	function fputcsv($fp, $record, $ls, $ts){

		if(empty($ts)){
			$data = implode($ls, $record)."\r\n";
			return fputs($fp, $data);
		}else
		{
			return fputcsv($fp, $record, $ls, $ts);
		}
	}

	function export($fp){

		parent::export($fp);

		global $GO_USERS, $lang, $GO_MODULES;

		if(count($this->headers))
			$this->fputcsv($fp, $this->headers, $this->list_separator, $this->text_separator);


		while($record = $this->db->next_record())
		{
			if(!count($this->columns))
			{
				foreach($record as $key=>$value)
				{
					$this->columns[]=$key;
					$this->headers[]=$key;
				}
				$this->fputcsv($fp, $this->headers, $this->list_separator, $this->text_separator);
			}

			/*if(is_array($this->q) && isset($this->q['method']))
			{
				call_user_func_array(array($this->q['class'], $this->q['method']),array(&$record, $cf));
			}*/
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
			$this->fputcsv($fp, $values,$this->list_separator, $this->text_separator);
		}
	}
}
