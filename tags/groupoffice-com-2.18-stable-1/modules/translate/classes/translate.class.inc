<?php
/*
Copyright Intermesh 2003
Author: Merijn Schering <mschering@intermesh.nl>
Version: 1.0 Release date: 2 Januari 2004

Part of the Group-Office Professional license
*/

if (!defined('T_ML_COMMENT')) {
	define('T_ML_COMMENT', T_COMMENT);
} else {
	define('T_DOC_COMMENT', T_ML_COMMENT);
}
		
class translate extends db
{
	function translate()
	{
		$this->db();
	}

	function get_language_files($src_lang='en')
	{
		global $GO_CONFIG, $GO_CONFIG;

		require_once($GO_CONFIG->class_path.'filesystem.class.inc');
		$fs = new filesystem(true);

		$folders = $fs->get_folders_sorted($GO_CONFIG->root_path.'language');

		$files = array();

		foreach($folders as $folder)
		{
			$file = $folder['path'].'/'.$src_lang.'.inc';
			if(file_exists($file))
			{
				$files[$folder['name']]=$file;
			}
		}

		$folders = $fs->get_folders_sorted($GO_CONFIG->root_path.'modules');

		foreach($folders as $folder)
		{
			$file = $folder['path'].'/language/'.$src_lang.'.inc';
			if(file_exists($file))
			{
				$files[$folder['name']]=$file;
			}
		}
		return $files;
	}

	function get_vars($file)
	{
		$file = file_get_contents($file);




		/*
		* T_ML_COMMENT does not exist in PHP 5.
		* The following three lines define it in order to
		* preserve backwards compatibility.
		*
		* The next two lines define the PHP 5 only T_DOC_COMMENT,
		* which we will mask as T_ML_COMMENT for PHP 4.
		*/
		

		$tokens = token_get_all($file);
		//var_dump($tokens);

		$file='';
		foreach ($tokens as $token) {
			if (is_string($token)) {
				// simple 1-character token
				$file .= $token;
			} else {
				// token array
				list($id, $text) = $token;

				switch ($id) {
					case T_COMMENT:
					case T_ML_COMMENT: // we've defined this
					case T_DOC_COMMENT: // and this
					// no action on comments
					break;

					default:
						// anything else -> output "as is"
						$file .= $text;
						break;
				}
			}
		}




		$file = str_replace("\r\n","\n", $file);
		//$file = str_replace("\n","", $file);


		$lines = explode("\n", $file);
		$file = '';
		foreach($lines as $line)
		{
			if(!strstr($line,'<?php') && !strstr($line, '?>') && !strstr($line, 'get_fallback'))
			{
				$end = strpos($line, '\';');
				if(!$end)
				{
					$end = strpos($line, '";');
				}
				if($end)
				{
					$line = substr($line,0, $end+2);
				}

				
				$file .= trim($line);
				
			}

		}

		$lines = explode(';$',substr($file,1,-1));

		$vars = array();
		foreach($lines as $line)
		{
			$ispos = strpos($line,'=');
			if($ispos)
			{
				$name= trim(substr($line, 0, $ispos));

				//$end = strpos($line, ';');


				$value = trim(substr($line,$ispos+2,-1));
				$value = str_replace('\\n',"\n",$value);
				$value = stripslashes($value);

				
				$vars[$name]=$value;
				//$vars[$name]=stripslashes($value);
			}
		}

		return $vars;
	}

	function files_are_different($src_file, $dest_file)
	{
		$src_vars = $this->get_vars($src_file);
		$dest_vars = $this->get_vars($dest_file);
		
		/*
		Uncommenting this will process all files
		foreach($dest_vars as $name=>$value)
		{
			$vars[$name]=str_replace('\'', '\\\'', $value);
		}
		$this->save_file($dest_file, $vars);*/

		return (count($src_vars)!=count($dest_vars));
	}

	function save_file($file, $vars)
	{
		$fp =fopen($file,'w+');
		fputs($fp, "<?php\n");
		fputs($fp, "//Translated with Group-Office translate module\n");
		if(strstr($file,'modules'))
		{
			fputs($fp, 'require_once($GO_LANGUAGE->get_fallback_language_file(\''.basename(dirname(dirname($file))).'\'));'."\n");
		}else {
			fputs($fp, 'require_once($GO_LANGUAGE->get_fallback_base_language_file(\''.basename(dirname($file)).'\'));'."\n");
		}


		foreach($vars as $name=>$value)
		{
			if(!empty($value))
			{
				
				$value = str_replace("\r\n","\n",$value);
				
				if(strpos($value,"\n"))
				{
					$line = '$'.$name.'="'.str_replace("\n",'\n',stripslashes($value)).'";'."\n";
				}else {
					$line = '$'.$name.'=\''.str_replace("\n",'\n',$value).'\';'."\n";
				}
				fputs($fp, $line);
			}
		}
		fputs($fp, "?>");
		fclose($fp);
	}

	function get_iso_codes()
	{
		$sql = "SELECT iso_code_2 FROM countries ORDER BY iso_code_2 ASC";
		$this->query($sql);
		return $this->num_rows();

	}
}
