<?php
class GO_Base_Db_Columns{
	
	public static $forceLoad = false;
	
	private static $_columns=array();
	
	public static function getColumns(GO_Base_Db_ActiveRecord $model) {
		
//		$className = $model->className();
		$tableName = $model->tableName();
		$cacheKey = 'modelColumns_'.$tableName;
		
		if(self::$forceLoad){
			unset(self::$_columns[$tableName]);
			GO::cache()->delete($cacheKey);
		}
		
		if(isset(self::$_columns[$tableName]) && !self::$forceLoad){
			return self::$_columns[$tableName];
		}elseif(($columns = GO::cache()->get($cacheKey))){
//			GO::debug("Got columns from cache for $tableName");
			self::$_columns[$tableName]=$columns;
			return self::$_columns[$tableName];
		}else
		{	
//			GO::debug("Loading columns for $tableName");
			self::$_columns[$tableName]=array();
			$sql = "SHOW COLUMNS FROM `" . $tableName. "`;";
			$stmt = $model->getDbConnection()->query($sql);
			while ($field = $stmt->fetch()) {					
				preg_match('/([a-zA-Z].*)\(([1-9].*)\)/', $field['Type'], $matches);
				if ($matches) {
					$length = $matches[2];
					$type = $matches[1];
				} else {
					$type = $field['Type'];
					$length = 0;
				}

				$required=false;
				$gotype = 'textfield';

				$pdoType = PDO::PARAM_STR;
				switch ($type) {
					case 'int':
					case 'tinyint':
					case 'bigint':

						$pdoType = PDO::PARAM_INT;
						if($length==1 && $type=='tinyint')
							$gotype='boolean';
						else
							$gotype = '';

						$length = 0;

						break;		

					case 'float':
					case 'double':
					case 'decimal':
						$pdoType = PDO::PARAM_STR;
						$length = 0;
						$gotype = 'number';
						break;

					case 'mediumtext':
					case 'longtext':
					case 'text':
						$gotype = 'textarea';
						break;

					case 'mediumblob':
					case 'longblob':
					case 'blob':
						$gotype = 'blob';
						break;

					case 'date':
						$gotype='date';
						break;
				}

				switch($field['Field']){
					case 'ctime':
					case 'mtime':
						$gotype = 'unixtimestamp';			
						break;
					case 'name':
						$required=true;							
						break;
					case 'user_id':
						$gotype = 'user';
						break;
				}

				$default = $field['Default'];

				//TODO: Why no default null value here???
				if($field['Null']=='NO' && is_null($default) && strpos($field['Extra'],'auto_increment')===false)
					$default='';

				//$required = is_null($default) && $field['Null']=='NO' && strpos($field['Extra'],'auto_increment')===false;

				self::$_columns[$tableName][$field['Field']]=array(
						'type'=>$pdoType,
						'required'=>$required,
						'length'=>$length,
						'gotype'=>$gotype,
						'default'=>$default,
						'dbtype'=>$type
				);

			}
			
			GO::cache()->set($cacheKey, self::$_columns[$tableName]);
			
			return self::$_columns[$tableName];			
		}		
	}
}