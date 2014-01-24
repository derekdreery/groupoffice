<?php
$cron = new GO_Base_Cron_CronJob();
		
$cron->name = 'Recalculate user quota';
$cron->active = true;
$cron->runonce = true;
$cron->minutes = '24';
$cron->hours = '2';
$cron->monthdays = '*';
$cron->months = '*';
$cron->weekdays = '*';
$cron->job = 'GO_Files_Cron_RecalculateDiskUsage';		

$cron->save();