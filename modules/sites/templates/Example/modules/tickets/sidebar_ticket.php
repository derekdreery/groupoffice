<?php 
$closed = '<font style="color:red;">not at</font>';
$open = '<font style="color:green;">at</font>';

$officehours = false;

$weekday = date('w');
$hour = date('H');
$minutes = date('i');

if($weekday!=0 && $weekday!=6){
	if($hour >= 8 && $hour < 17){
		if($hour == 8 && $minutes < 30)
			$officehours = false;
		else
			$officehours = true;
	}
}

//if(!GO_Base_Util_Date::is_on_free_day(time())){
//	$officehours = true;
//}
?>

<div class="subkader-right">
	<h1>Ticket Responses</h1>
	<p>We try to respond to your tickets as soon as possible.</p>
	<p>&nbsp;</p>
	<p>We are currently <?php echo $officehours?$open:$closed; ?> the office.</p>
	<p>&nbsp;</p>
	<p>Our opening hours:</p>
	<p>Monday till Friday from 08:30 till 17:00 GMT+1</p>
</div>