<?php
/**
 * @copyright Intermesh 2007
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1615 $ $Date: 2008-04-25 16:18:36 +0200 (vr, 25 apr 2008) $
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 * 
 * Group-Office system requirements test script
 * 
 */

/**
* Format a size to a human readable format.
* 
* @param	int $size The size in bytes
* @param	int $decimals Number of decimals to display
* @access public
* @return string
*/

function format_size($size, $decimals = 1) {
	switch ($size) {
		case ($size > 1073741824) :
			$size = number_format($size / 1073741824, $decimals, '.', ' ');
			$size .= " GB";
			break;

		case ($size > 1048576) :
			$size = number_format($size / 1048576, $decimals, '.', ' ');
			$size .= " MB";
			break;

		case ($size > 1024) :
			$size = number_format($size / 1024, $decimals, '.', ' ');
			$size .= " KB";
			break;

		default :
			number_format($size, $decimals, '.', ' ');
			$size .= " bytes";
			break;
	}
	return $size;
}

function return_bytes($val) {
   $val = trim($val);
   $last = strtolower($val{strlen($val)-1});
   switch($last) {
       // The 'G' modifier is available since PHP 5.1.0
       case 'g':
           $val *= 1024;
       case 'm':
           $val *= 1024;
       case 'k':
           $val *= 1024;
   }
   return $val;
}

function whereis($cmd)
{
	exec('whereis '.$cmd, $return);
	
	if(isset($return[0]))
	{
		$locations = explode(' ', $return[0]);
		if(isset($locations[1]))
		{
			return $locations[1];
		}
		return false;
	}
}
?>
<h1 style="font-family: Arial, Helvetica;font-size: 18px;">Group-Office test script</h1>

<table border="0" style="font-family: Arial, Helvetica;font-size:12px;">
<tr>
	<td>Server software:</td>
	<td><?php echo $_SERVER['SERVER_SOFTWARE']; ?></td>
</tr>
<tr>
	<td valign="top">PHP version:</td>
	<td>
	<?php 
	if(function_exists('version_compare') && version_compare( phpversion(), "5.2", ">="))
	{
		echo 'Ok ('.phpversion().')';
	}else
	{
		$fatal_error = true;
		echo '<span style="color: red;">Fatal error: Your PHP version is too old to run Group-Office. PHP 5.2 is required</span>';
	}
	?></td>
</tr>
<tr>
	<td valign="top">MySQL support:</td>
	<td>
	<?php
	if(function_exists('mysql_connect'))
	{
		echo 'Ok';
	}else
	{
		$fatal_error = true;
		echo '<span style="color: red;">Fatal error: The MySQL extension is required. So is the MySQL server.</span>';
	}
	?>
	</td>
</tr>
<tr>
	<td valign="top">
	IMAP support:
	</td>
	<td>
	<?php
	if(function_exists('imap_open'))
	{
		echo 'Ok';
	}else
	{
		echo '<span style="color: red;">Warning: IMAP extension not installed, E-mail module will not work.</span>';
	}
	?>
	</td>
</tr>
<tr>
	<td valign="top">File upload support:</td>
	<td>
	<?php
	if(ini_get('file_uploads') == '1')
	{
		echo 'Ok';
	}else
	{
		$fatal_error = true;
		echo '<span style="color: red;">Fatal error: File uploads are disabled. Please set file_uploads=On in php.ini.</span>';
	}
	?>
	</td>
</tr>
<tr>
	<td valign="top">Safe mode:</td>
	<td>
	<?php
	if(ini_get('safe_mode') == '1')
	{
		echo '<span style="color: red;">Warning: Safe mode is enabled. This may cause trouble with the filesystem module and Synchronization. If you can please set safe_mode=Off in php.ini.</span>';
	}else
	{
		echo 'Ok';		
	}
	?>
	</td>
</tr>
<tr>
	<td valign="top">Open base_dir:</td>
	<td>
	<?php
	if(ini_get('open_basedir')!='')
	{
		echo '<span style="color: red;">Warning: open_basedir is enabled. This may cause trouble with the filesystem module and Synchronization.</span>';
	}else
	{
		echo 'Ok';		
	}
	?>
	</td>
</tr>

<tr>
	<td valign="top">Calendar functions:</td>
	<td>
	<?php
	if(!function_exists('easter_date'))
	{
		echo '<span style="color: red;">Warning: Calendar functions not available. The Group-Office calendar won\'t be able to generate all holidays for you. Please compile PHP with --enable-calendar.</span>';
	}else
	{
		echo 'Ok';		
	}
	?>
	</td>
</tr>
<tr>
	<td valign="top">Memory limit:</td>
	<td>
	<?php
	$memory_limit = ini_get('memory_limit');
	if(strlen($memory_limit)==0)
	{
		echo '<span style="color: red;">Warning: Your memory limit setting is not set. This might not be a problem but it\'s recommended to allow at least 32MB of memory for a PHP script to run.</span>';
	}else
	{
		$memory_limit = return_bytes(ini_get('memory_limit'));
		if($memory_limit>=32*1024*1024)
		{
			echo 'Ok';
		}else
		{
			echo '<span style="color: red;">Warning: Your memory limit setting ('.Number::format_size($memory_limit).') is less then 32MB. It\'s recommended to allow at least 32 MB.</span>';
		}
	}
	?>
	</td>
</tr>
<tr>
	<td valign="top">Register globals:</td>
	<td>
	<?php
	if(ini_get('register_globals')=='1')
	{
		//$fatal_error = true;
		echo '<span style="color: red;">Warning: register_globals is enabled in php.ini. We recently made Group-Office compatible with this setting. This is still in a testing stage.</span>';
	}else
	{
		echo 'Ok';
	}
	?>
	</td>
</tr>
<tr>
	<td valign="top">Error logging:</td>
	<td>
	<?php	

	if(ini_get('log_errors')!='1')
	{
		//$fatal_error = true;
		echo '<span style="color: red;">Warning: PHP error logging is disabled in php.ini. It\'s recommended that this feature is enabled.</span>';
	}else
	{
		echo 'Ok';
	}
	?>
	</td>
</tr>
<tr>
	<td valign="top">MIME types:</td>
	<td>
	<?php	
	if(!function_exists('mime_content_type') && !function_exists('finfo_open'))
	{
		echo '<span style="color: red;">Warning: mime-magic is not installed. Group-Office will no be able to determine MIME types of files. This may cause problems with file downloads.</span>';
	}else
	{
		echo 'Ok';
	}
	?>
	</td>
</tr>
<tr>
	<td valign="top">MultiByte string functions:</td>
	<td>
	<?php	
	if(!function_exists('mb_detect_encoding'))
	{
		echo '<span style="color: red;">Warning: php-mbstring is not installed. Problems with non-ascii characters in e-mails and filenames might occur.</span>';
	}else
	{
		echo 'Ok';
	}
	?>
	</td>
</tr>
<tr>
	<td valign="top">libwbxml:</td>
	<td>
	<?php	
	$wbxml2xml = whereis('wbxml2xml') ? whereis('wbxml2xml') : '/usr/bin/wbxml2xml';
	$xml2wbxml = whereis('xml2wbxml') ? whereis('xml2wbxml') : '/usr/bin/xml2wbxml';;
	if(!is_executable($wbxml2xml) || !is_executable($xml2wbxml))
	{
		//$fatal_error = true;
		echo '<span style="color: red;">Warning: libwbxml2 is not installed. Synchronization will not work!</span>';
	}else
	{
		echo 'Ok';
	}
	?>
	</td>
</tr>
<tr>
	<td valign="top">TAR Compression:</td>
	<td>
	<?php	
	$tar = whereis('tar') ? whereis('tar') : '/bin/tar';
	
	if(!is_executable($tar))
	{
		//$fatal_error = true;
		echo '<span style="color: red;">Warning: tar is not installed or not executable.</span>';
	}else
	{
		echo 'Ok';
	}
	?>
	</td>
</tr>
<tr>
	<td valign="top">ZIP Compression:</td>
	<td>
	<?php	
	$zip = whereis('zip') ? whereis('zip') : '/usr/bin/zip';
	
	if(!is_executable($zip))
	{
		//$fatal_error = true;
		echo '<span style="color: red;">Warning: zip is not installed or not executable.</span>';
	}else
	{
		echo 'Ok';
	}
	?>
	</td>
</tr>
<tr>
	<td valign="top">TNEF:</td>
	<td>
	<?php	
	$tnef = whereis('tnef') ? whereis('tnef') : '/usr/bin/tnef';
	
	if(!is_executable($tnef))
	{
		//$fatal_error = true;
		echo '<span style="color: red;">Warning: tnef is not installed or not executable. you can\'t view winmail.dat attachments in the email module.</span>';
	}else
	{
		echo 'Ok';
	}
	?>
	</td>
</tr>
<tr>
	<td colspan="2">
	<br />
	<b>Use this information for your Group-Office Professional license:</b>
	</td>
</tr>

<tr>
	<td valign="top">Server name:</td>
	<td>
	<?php	
	echo $_SERVER['SERVER_NAME'];
	?>
	</td>
</tr>
<tr>
	<td valign="top">Server IP:</td>
	<td>
	<?php	
	echo  gethostbyname($_SERVER['SERVER_NAME']);
	?>
	</td>
</tr>
</table>

<?php
if(isset($fatal_error))
{
	echo '<p style="font-family: Arial, Helvetica;font-size: 12px;color:red;">Fatal errors occured. Group-Office will not run properly with current system setup!</p>';
}else
{
	echo '<p style="font-family: Arial, Helvetica;font-size: 12px;">Passed, Group-Office should run on this machine</p>';
}
