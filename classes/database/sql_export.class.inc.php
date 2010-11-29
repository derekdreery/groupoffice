<?php
class sql_export {

	function array_to_insert($table, $array, $mode='INSERT'){
		$field_values = array_values($array);
		$field_values = array_map('mysql_real_escape_string',$array);

		$sql = $mode;
		$sql .= " INTO `$table` (`".implode('`,`', array_keys($array))."`) VALUES ".
				"('".implode("','", $field_values)."')";

		return $sql;
	}

}