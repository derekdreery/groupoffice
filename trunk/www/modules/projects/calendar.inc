<?php
/*////////////////////////////////////////////////////////////////////////////////
//																				//
// Author: Merijn Schering <mschering@intermesh.nl>           						//
// Version: 1.0 Release date: 14 March 2003										//
//																				//
////////////////////////////////////////////////////////////////////////////////*/

$time = time();
$today = date("j", $time);
$this_year = date("Y", $time);
$this_month = date("m", $time);

$day = isset($day) ? $day : $today;
$month = isset($month) ? $month : $this_month;
$year = isset($year) ? $year : $this_year;


$daystart = 1;

$years[0] = $year - 2;
$years[1] = $year - 1;
$years[2] = $year;
$years[3] = $year + 1;
$years[4] = $year + 2;

// NUMBER OF DAYS IN MONTH
$days_in_month = mktime (0,0,0,$month+1,0,$year);
$days_in_month = (strftime ("%d",$days_in_month));

if ($day > $days_in_month)
{
	$day = $day - $days_in_month;
	if ($month < 12)
	{
		$month++;
	}else
	{
		$month=1;
		$year++;
	}

	$days_in_month = mktime (0,0,0,$month+1,0,$year);
	$days_in_month = (strftime ("%d",$days_in_month));
}

$first_day_of_month = date("w", mktime(0,0,0,$month,1,$year));

$month_numbers[0] = '1';
$month_numbers[1] = '2';
$month_numbers[2] = '3';
$month_numbers[3] = '4';
$month_numbers[4] = '5';
$month_numbers[5] = '6';
$month_numbers[6] = '7';
$month_numbers[7] = '8';
$month_numbers[8] = '9';
$month_numbers[9] = '10';
$month_numbers[10] = '11';
$month_numbers[11] = '12';
$dropbox = new dropbox();
$dropbox->add_arrays($month_numbers, $months);
$dropbox->print_dropbox('month',$month, 'onchange="document.forms[0].submit()"');

$dropbox = new dropbox();
$dropbox->add_arrays($years, $years);
$dropbox->print_dropbox('year',$year, 'onchange="document.forms[0].submit()"');

echo '<input type="hidden" name="day" value="'.$day.'" />';

echo '<table border="0" cellpadding="0" cellspacing="3">';
echo '<tr><td>&nbsp;</td>';

$day_number = $daystart;
for ($i=0;$i<7;$i++)
{
	if ($day_number == 7) $day_number = 0;
	echo '<td align="center" class="small">'.$days[$day_number].'</td>';
	$day_number++;
}
echo '</tr>';

$cellcount=$first_day_of_month-$daystart;
if ($cellcount < 0) $cellcount = 7 + $cellcount;

echo '<tr>';
//$week_number = intval(strftime("%V",mktime(0,0,0,$month,1,$year)));
$week_number = date("W", mktime(0,0,0,$month,1,$year));

echo '<td class="small"><i>'.$week_number.'</i>&nbsp;</td>';
for ($i=1;$i<=$cellcount;$i++)
{
	echo '<td>&nbsp;</td>';
}

for ($i=1;$i<=$days_in_month;$i++)
{
	if ($cellcount == 7)
	{
		echo "</tr><tr>\n";
		$week_number++;
		echo '<td class="small"><i>'.$week_number.'</i>&nbsp;</td>';
		$cellcount=0;
	}
	$cellcount++;
	if ($i == $day)
		$class="HoverLinkSelected";
	else
		$class="HoverLink";

	echo '<td align="center"><a class="'.$class.'" href="javascript:post_day('.$i.')">'.$i.'</a></td>';

}
$remaining_cells = 7-$cellcount;

for ($i=0;$i<$remaining_cells;$i++)
{
	echo '<td>&nbsp;</td>';
}

echo '</tr>';
echo '</table>';

?>
<script type="text/javascript" language="javascript">

function post_day(day)
{
	document.forms[0].day.value=day;
	document.forms[0].submit();
}

</script>