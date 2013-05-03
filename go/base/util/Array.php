<?php

class GO_Base_Util_Array {

	/**
	 * Merge array recurively. 
	 * 
	 * array_merge_recursive from php does not handle string elements right. 
	 * It does not overwrite them but it creates unwanted sub arrays.
	 * 
	 * @param array $array1
	 * @param array $array2
	 * @return array
	 */
	public static function mergeRecurive(array $array1, array $array2) {
		foreach ($array2 as $key => $value) {
			if (is_array($value) && isset($array1[$key])) {
				$array1[$key] = self::mergeRecurive($array1[$key], $value);
			} else {
				$array1[$key] = $value;
			}
		}

		return $array1;
	}
}