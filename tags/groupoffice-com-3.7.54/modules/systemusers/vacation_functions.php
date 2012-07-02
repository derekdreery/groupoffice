<?php
/**
 * This function generates a forwarding line suitable for plain old
 * .forward files.
 * @param username
 *   The user name.
 * @return
 *   The line to be appended to the .forward file.
 */
function genForwardDef($username, $email)
{
	return '\\'.$username.', "|/usr/bin/vacation -a '.$email.' '.$username.'"'."\n";
}

/**
 * This function generates a forwarding line suitable for Exim
 * .forward files.
 * @param username
 *   The user name.
 * @return
 *   The line to be appended to the .forward file.
 */
function genForwardExim($username, $email)
{
	return 'unseen pipe "/usr/bin/vacation -a '.$email.' '.$username.'"'."\n";
}

/**
 * This function generates a forwarding line suitable for Sieve
 * .forward files.
 * @param username
 *   The user name.
 * @return
 *   The line to be appended to the .forward file.
 * @note
 *   This function is not implemented yet!
 */
function genForwardSieve($username, $email)
{
	return '# no support for Sieve filters yet';
}

/**
 * Returns the correct string for inclusion in the .forward file.
 * This is determined by looking into the file's contents.
 * @param lines
 *   The contents of the .forward file, as read by the file()
 *   function.
 * @param username
 *   The user name.
 * @return
 *   The line to be appended to the .forward file.
 */
function getVacation($lines, $username, $email)
{
	if (count ($lines) > 0 && $lines[0] == "# Exim filter\n") {
		return genForwardExim ($username, $email);
	}
	else if (count ($lines) > 0 && $lines[0] == "# Sieve filter\n") {
		return genForwardSieve ($username, $email);
	}
	else {
		return genForwardDef ($username, $email);
	}
}

/**
 * Returns true if the line is a .forward comment.
 * @param
 *   The line to check.
 * @note
 *   Only one-line comments are currently supported.
 */
function isComment($line)
{
	return strlen ($line) > 0 && $line[0] == '#';
}

/**
 * Writes data to a file, truncating existing and creating non-existing
 * files.
 * @param filename
 *   The file to write to.
 * @param contents
 *   A string containing the data to be written.
 */
function writeFile($filename, $contents)
{
	if (!$fp = fopen($filename, 'w')) {
		go_log(LOG_INFO, "Cannot open file for writing ($filename)");
	}
	else {
		if (fwrite($fp, $contents) === false) {
			/* kristov: log also write errors instead of exiting */
			go_log(LOG_ERR, "Cannot write to file ($filename)");
		}
		fclose($fp);
	}
}

/**
 * Appends data to a file, creating non-existing files.
 * @param filename
 *   The file to append to.
 * @param contents
 *   A string containing the data to be appended.
 */
function appendFile($filename, $contents)
{
	if (!$fp = fopen($filename, 'a')) {
		go_log(LOG_INFO, "Cannot open file for appending ($filename)");
	}
	else {
		if (fwrite($fp, $contents) === false) {
			/* kristov: log also append errors instead of exiting */
			go_log(LOG_ERR, "Cannot append to file ($filename)");
		}
		fclose($fp);
	}
}

/**
 * Includes a vacation rule into the .forward file.
 * @param filename
 *   The (fully qualified) name of the .forward file.
 * @param username
 *   The user name.
 * @note
 *   This function does not check whether the rule to be added already
 *   exists. You should use excludeVacation() before calling this
 *   function to avoid duplicate lines.
 */
function includeVacation($filename, $username, $email)
{
	if (!file_exists ($filename)) {
		$vacation = genForwardDef ($username, $email);
	}
	else {
		$lines = file($filename);
		$vacation = getVacation ($lines, $username, $email);
	}

	appendFile ($filename, $vacation."\n");
}

/**
 * Removes a previously inserted vacation rule from the .forward file.
 * @param filename
 *   The (fully qualified) name of the .forward file.
 * @param username
 *   The user name.
 * @note
 *   If the user has put a line identical to the vacation rule by herself,
 *   it is also removed as the line(s) are found by string matching.
 */
function excludeVacation($filename, $username, $email)
{

	if (!file_exists ($filename))
		return;
	$lines = file($filename);
	if ($lines === false)
		return;

	$vacation = getVacation ($lines, $username, $email);

	$result = "";
	foreach ($lines as $line) {
		if (strcmp ($line, $vacation) != 0)
			$result .= $line;
	}

	writeFile ($filename, $result);
}


function includeForward($filename, $to, $username='')
{
	$forward = "#GO forward def. Do not remove this line\n";
	if(!empty($username))
	{
		$forward .= '\\'.$username.',';
	}
	$forward .= $to."\n";
	
	appendFile ($filename, $forward);
}


function removeForward($filename)
{
	if (!file_exists ($filename))
		return;
	$lines = file($filename);
	if ($lines === false)
		return;


	$result = "";
	for($i=0;$i<count($lines);$i++)
	{
		
		if (strcmp ($lines[$i], "#GO forward def. Do not remove this line\n") != 0)
		{
			$result .= $lines[$i];
		}else
		{
			$i++;
		}
	}
	
	//echo $result;
	writeFile ($filename, $result);
}

