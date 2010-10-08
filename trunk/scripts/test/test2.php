#!/usr/bin/php
<?php

/*
array
  0 => string '-P15DT5H0M20S' (length=13)
  1 => string '-' (length=1)
  2 => string '15D' (length=3)
  3 => string '5H' (length=2)
  4 => string '0M' (length=2)
  5 => string '20S' (length=3)
 * 
 */

$t="-P15DT5H0M20S";

//$t="-P15DT2M";

$t="-PT15M";

preg_match('/(-?)P([0-9]+[WD])?T?([0-9]+H)?([0-9]+M)?([0-9]+S)?/', $t, $matches);
var_dump($matches);


$negative = $matches[1]=='-' ? -1 : 1;

$days = 0;
$weeks = 0;
$hours=0;
$mins=0;
$secs = 0;
for($i=2;$i<count($matches);$i++){
	$d = substr($matches[$i],-1);
	switch($d){
		case 'D':
			$days += intval($matches[$i]);
			break;
		case 'W':
			$weeks += intval($matches[$i]);
			break;
		case 'H':
			$hours += intval($matches[$i]);
			break;
		case 'M':
			$mins += intval($matches[$i]);
			break;
		case 'S':
			$secs += intval($matches[$i]);
			break;
	}
}

$time = $negative*(($weeks * 60 * 60 * 24 * 7) + ($days * 60 * 60 * 24) + ($hours * 60 * 60) + ($mins * 60) + ($secs));

var_dump($time);
