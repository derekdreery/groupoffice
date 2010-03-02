<?php
class file_customfield_type extends default_customfield_type{
	function format_for_display($field, &$record, $fields){
		global $GO_MODULES;

		if(!empty($record[$field['dataname']])){
			$filerecord['data']['path']=$record[$field['dataname']];
			$filerecord['data']['extension']=File::get_extension($record[$field['dataname']]);

			$record[$field['dataname']]='<a href="#" onclick=\'GO.files.openFile('.json_encode($filerecord).');\' title="'.$record[$field['dataname']].'">'.utf8_basename($record[$field['dataname']]).'</a>';
		}
	}
}