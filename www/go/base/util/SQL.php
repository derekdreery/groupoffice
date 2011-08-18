<?php

class GO_Base_Util_SQL {

	/**
	 * Get's all queries from an SQL dump file in an array
	 *
	 * @param	string $file The absolute path to the SQL file
	 * @access public
	 * @return array An array of SQL strings
	 */
	public static function getSqlQueries($file) {
		$sql = '';
		$queries = array();
		if ($handle = fopen($file, "r")) {
			while (!feof($handle)) {
				$buffer = trim(fgets($handle, 4096));
				if ($buffer != '' && substr($buffer, 0, 1) != '#' && substr($buffer, 0, 1) != '-') {
					$sql .= $buffer;
				}
			}
			fclose($handle);
		} else {
			die("Could not read SQL dump file $file!");
		}
		$length = strlen($sql);
		$in_string = false;
		$start = 0;
		$escaped = false;
		for ($i = 0; $i < $length; $i++) {
			$char = $sql[$i];
			if ($char == '\'' && !$escaped) {
				$in_string = !$in_string;
			}
			if ($char == ';' && !$in_string) {
				$offset = $i - $start;
				$queries[] = substr($sql, $start, $offset);

				$start = $i + 1;
			}
			if ($char == '\\') {
				$escaped = true;
			} else {
				$escaped = false;
			}
		}
		return $queries;
	}

}